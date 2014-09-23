<?php
/**
 * Created by PhpStorm.
 * User: hugo
 * Date: 18/09/14
 * Time: 17:27
 */

namespace Corrigeaton\Bundle\ReportBundle\Entity\Repository;


use Corrigeaton\Bundle\ReportBundle\Entity\Report;
use Doctrine\ORM\EntityRepository;

class ReportRepository extends EntityRepository{
    public function getTenLastUnfinished()
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('r')
            ->from('CorrigeatonReportBundle:Report','r')
            ->setMaxResults(10)
            ->where('r.isFinished = false')
            ->orderBy("r.date","ASC")
            ->getQuery()
            ->getResult();
    }


    public function countReport($isFinished, Report $r=null)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('count(r)')
            ->from('CorrigeatonReportBundle:Report','r')
            ->where('r.isFinished= :isFinished')
            ->setParameter('isFinished',$isFinished);

        if(!empty($r)){
            $query->andWhere('r.report = :report')
                ->setParameter('report',$r);
        }

        return $query
            ->getQuery()
            ->getSingleScalarResult();
    }

} 