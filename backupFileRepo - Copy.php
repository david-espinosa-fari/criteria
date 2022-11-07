<?php


namespace MonitoringServiceBundle\Infrastructure;

use Shared\SearchCriteria\Domain\Comparision;
use Shared\SearchCriteria\Domain\Criteria;
use Shared\SearchCriteria\Domain\Exceptions\CriteriaError;
use Shared\SearchCriteria\Domain\IStringConditionExpressions;
use Shared\SearchCriteria\Domain\ValueObjects\Operator;


class BackupFileRepo
{

    private Connection $connect;
    /**
     * @var IStringConditionExpressions
     */
    private $expresionBuilder;


    private function __construct(IStringConditionExpressions $expresionBuilder) 
    {
        $this->expresionBuilder = $expresionBuilder;
    }

    public function findMonitoringBackupFileByCriteria(Criteria $criteria): MonitoringBackupFileList
    {
        $sql = 'SELECT * FROM yourSuperTable '.$this->expresionBuilder->createStringConditionExpression($criteria);

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

        $result = $this->executeQuery($stmt);

        if (empty($result)) {
            throw 
        }

       return new Lerele($result);
    }
 
}
