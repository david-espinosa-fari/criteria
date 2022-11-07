<?php


namespace GPM\MonitoringServiceBundle\Infrastructure;


use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Statement;
use GPM\CoreBundle\Domain\ConnectDbRepository;
use GPM\MonitoringServiceBundle\Domain\Exceptions\MonitoringBackupFileException;
use GPM\MonitoringServiceBundle\Domain\IMonitoringBackupFileRepo;
use GPM\MonitoringServiceBundle\Domain\IMonitoringStorage;
use GPM\MonitoringServiceBundle\Domain\MonitoringBackupFile;
use GPM\MonitoringServiceBundle\Domain\MonitoringBackupFileList;
use GPM\Shared\Measurements\Service\Measurements;
use GPM\Shared\SearchCriteria\Domain\Comparision;
use GPM\Shared\SearchCriteria\Domain\Criteria;
use GPM\Shared\SearchCriteria\Domain\Exceptions\CriteriaError;
use GPM\Shared\SearchCriteria\Domain\IStringConditionExpressions;
use GPM\Shared\SearchCriteria\Domain\ValueObjects\Operator;
use GPM\Shared\Util\GpmFile;
use Symfony\Component\HttpFoundation\File\File;
use Throwable;

class MonitoringBackupFileRepo implements IMonitoringBackupFileRepo
{
    private static $instance;
    private static Measurements $measurements;
    private Connection $connect;
    private IMonitoringStorage $storageEngine;
    /**
     * @var IStringConditionExpressions
     */
    private $expresionBuilder;


    private function __construct(
        ConnectDbRepository $connection,
        IMonitoringStorage $storageEngine,
        IStringConditionExpressions $expresionBuilder
    ) {
        $this->connect = $connection->getConnection();
        $this->storageEngine = $storageEngine;
        $this->expresionBuilder = $expresionBuilder;

    }

    public static function getInstance(
        ConnectDbRepository $connection,
        Measurements $measurement,
        IMonitoringStorage $storageEngine,
        IStringConditionExpressions $expresionBuilder
    ): self {
        if (null === self::$instance) {
            self::$instance = new self($connection, $storageEngine, $expresionBuilder);
        }
        self::$measurements = $measurement;

        return self::$instance;
    }

    public function remove(MonitoringBackupFile $backupFile): void
    {
        try {
            $this->connect->beginTransaction();
            $this->connect->delete(
                'tMonitoringBackupFile',
                [
                    'monitoringBackupfileId' => (string)$backupFile,
                ]
            );
            $this->storageEngine->delete($backupFile);
            $this->connect->commit();
        } catch (Throwable $e) {
            $this->connect->rollBack();
            throw new MonitoringBackupFileException(
                sprintf('<%s> Message: <%s>', static::class, $e->getMessage()), 500, $e
            );
        }
    }

    public function save(MonitoringBackupFile $backupFile): void
    {
        try {

            $this->connect->beginTransaction();

            $this->insert($backupFile);

            $this->store($backupFile);

            $this->connect->commit();

        } catch (MonitoringBackupFileException $e) {
            $this->connect->rollBack();
            throw new MonitoringBackupFileException($e->getMessage(), $e->getCode());

        } catch (Throwable $e) {
            $this->connect->rollBack();

            throw new MonitoringBackupFileException(
                sprintf('<%s> Message: <%s>', static::class, $e->getMessage()), 500, $e
            );
        }
    }

    private function insert(MonitoringBackupFile $backupFile): void
    {

        self::$measurements->start(static::class, __FUNCTION__, 'Monitoring Service backup file mysql');
        try{
            $sql = 'INSERT INTO tMonitoringBackupFile(monitoringBackupfileId, monitoringHashBackupfile, serviceId, fileNameDisplayed, filenameStored, pathToFile, extensionFile, fileSize, createdAt, updatedOn)';
            $values = ' VALUES (:monitoringBackupfileId, :monitoringHashBackupfile, :serviceId, :fileNameDisplayed, :filenameStored, :pathToFile, :extensionFile, :fileSize, :createdAt, :updatedOn)';

            $stmt = $this->connect->prepare($sql.$values);

            $primitives = $backupFile->getObjectLikeArray();
            foreach ($primitives as $key => $paramValue) {
                $stmt->bindValue($key, $paramValue);
            }

            $stmt->executeStatement();
        }catch (Throwable $e)
        {
            if (str_contains($e->getMessage(), 'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry'))
            {
                throw new MonitoringBackupFileException(
                    sprintf('<%s> Message: MonitoringBackupFile already exist', static::class), 409
                );
            }
            throw $e;
        }


        self::$measurements->stop(static::class, __FUNCTION__, 'Monitoring Service backup file mysql');
    }

    private function store(MonitoringBackupFile $backupFile): void
    {
        self::$measurements->start(static::class, __FUNCTION__, 'Monitoring Service backup file storage');

        $this->storageEngine->store($backupFile);

        self::$measurements->stop(static::class, __FUNCTION__, 'Monitoring Service backup file storage');
    }

    public function totalAmountSpaceUsed(string $monitoringServiceId): int
    {
        $sql = 'SELECT SUM(fileSize) FROM tMonitoringBackupFile WHERE serviceId=:serviceId';

        $stmt = $this->connect->prepare($sql);

        $stmt->bindValue('serviceId', $monitoringServiceId);
        $result = $stmt->executeQuery()->fetchOne();

        return $result ?? 0;
    }

    public function findMonitoringBackupFileByCriteria(Criteria $criteria): MonitoringBackupFileList
    {
        $sql = 'SELECT * FROM tMonitoringBackupFile '.$this->expresionBuilder->createStringConditionExpression($criteria);

        $stmt = $this->connect->prepare($sql);

        try {
            foreach ($criteria->getComparisions() as $comparision) {
                assert($comparision instanceof Comparision);
                if ($comparision->getOperator() === Operator::CONTAINS) {
                    $stmt->bindValue($comparision->getField(), '%'.$comparision->getValue().'%');
                } elseif ('null' !== $comparision->getValue()) {
                    $stmt->bindValue($comparision->getField(), $comparision->getValue());
                }
            }
        } catch (CriteriaError $e) {
            //There are no comparisons, so keep moving
        }

        $result = $this->_executeQuery($stmt);

        if (empty($result)) {
            throw new MonitoringBackupFileException(
                sprintf('<%s> Message: MonitoringBackupFile not found', static::class), 404
            );
        }

        $tempMonitoringServiceList = [];
        foreach ($result as $row) {
            $tempMonitoringServiceList[] = $this->buildMonitoringBackupFile($row);
        }

        return new MonitoringBackupFileList($tempMonitoringServiceList);
    }

    /**
     * this method is public only to mock the return of this in test. Do not use it outside this class, treated as private
     * @param Statement $stmt
     * @throws Exception
     * @return array
     */

    public function _executeQuery(Statement $stmt): array
    {
        return $stmt->executeQuery()->fetchAllAssociative();
    }

    /**
     * @param array $row (
     *                   'monitoringBackupfileId' => 'a934813b-2c7c-4373-8bb2-62fc4ceafdec',
     *                   'monitoringHashBackupfile' => 'f70a7d16d814744005c78a02209df5d4',
     *                   'serviceId' => '8a4b43d1-574d-43f9-8ebc-e2280f0e9a9c',
     *                   'fileNameDisplayed' => 'sizeOk.gz',
     *                   'filenameStored' => 'f70a7d16d814744005c78a02209df5d4',
     *                   'pathToFile' => '/8a4b43d1-574d-43f9-8ebc-e2280f0e9a9c',
     *                   'extensionFile' => 'gz',
     *                   'fileSize' => '438049',
     *                   'createdAt' => '2022-10-31 09:23:16',
     *                   'updatedOn' => '2022-10-31 09:23:16',
     *                   )
     * @return MonitoringBackupFile
     */
    private function buildMonitoringBackupFile(array $row): MonitoringBackupFile
    {
        $absolutPathToFile = $this->storageEngine->getRootStoragePath().$row['pathToFile'].'/'.$row['filenameStored'].'.'.$row['extensionFile'];
        $file = new File($absolutPathToFile, true);
        $gpmFile = new GpmFile($file, $row['fileNameDisplayed']);

        return MonitoringBackupFile::build(
            $row['monitoringBackupfileId'],
            $row['serviceId'],
            $row['filenameStored'],
            $row['pathToFile'],
            $absolutPathToFile,
            $gpmFile,
            $row['createdAt'],
            $row['updatedOn']
        );
    }

}
