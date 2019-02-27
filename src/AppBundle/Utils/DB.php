<?php
namespace AppBundle\Utils;
use SQLite3;
class DB extends SQlite3
{
    public function __construct()
    {
        $path = '../web/db/meteo.db';
        $this->open($path);
    }
    public function getMeasurementByDelay($delay)
    {
        $sql = $this->prepare(
            "select * from meting
                where mtimestamp <= :dattim
                order by mtimestamp desc
                limit 1"
        );
        $sql2 = $this->prepare(
            "select ROUND(AVG(temperature), 2) as average, min(temperature) as min,max(temperature) as max
            from meting where mtimestamp between :mindate and :maxdate
            "
        );
        $time = time() - (365*24*60*60) - ($delay * 60);
        $time2 = time() - (365*24*60*60);
        $date = date('Y-m-d H:i:s', $time);
        $date2 = gmdate('Y-m-d', $time2);
        $specialeManierOmTijdTeBerekenen = gmdate("Y-M-d H:i:s",strtotime(gmstrftime($date2 . " 00:00:00")));
        $mindate = gmdate('Y-m-d H:i:s', strtotime($date2 . '00:00:00'));
        $maxdate = gmdate('Y-m-d H:i:s', strtotime($date2 . '23:59:00'));
        $sql->bindValue(':dattim', $date);
        $sql2->bindValue(':mindate', $mindate);
        $sql2->bindValue(':maxdate', $maxdate);
        $res = $sql->execute();
        $res2 = $sql2->execute();
        return array($res->fetchArray(), $res2->fetchArray());
    }
}