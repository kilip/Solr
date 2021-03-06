<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Bridge;

use Jobs\Entity\Location;
use DateTime;

/**
 * Static utility to do conversion
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since  0.26
 * @package Solr\Bridge
 */
class Util
{
    /**
     * Convert Location coordinate into acceptable solr document format
     * @param Location $location
     * @return string
     */
    public static function convertLocationCoordinates(Location $location)
    {
        $coordinates = $location->getCoordinates()->getCoordinates();
        $coordinate = doubleval($coordinates[1]) . '%' . doubleval($coordinates[0]);
        $coordinate = strtr($coordinate, [
            '%'=>',',
            ','=>'.'
        ]);
        return $coordinate;
    }

    /**
     * Converts a location String "c:<lon>,<lat>" in "<lon>,<lat>"
     * @param Location $location
     *
     * @return string
     */
    public static function convertLocationString($location)
    {
        $coordinate = ltrim($location, 'c:');
        $coordinate = strtr($coordinate, [
            ',' => '.',
            ':' => ','
        ]);
        return $coordinate;
    }

    /**
     * Convert date time into acceptable solr document format
     *
     * @param DateTime $date
     * @return string
     */
    public static function convertDateTime(DateTime $date)
    {
        return $date->setTimezone(new \DateTimeZone('UTC'))->format(Manager::SOLR_DATE_FORMAT);
    }

    /**
     * Convert Solr date into a PHP DateTime object
     *
     * @param string $solrDate
     * @return DateTime
     */
    public static function convertSolrDateToPhpDateTime($solrDate)
    {
        $solrDate = trim($solrDate);
        $dateTime = DateTime::createFromFormat(Manager::SOLR_DATE_FORMAT, $solrDate);
        $valid = $dateTime && ($dateTime->format(Manager::SOLR_DATE_FORMAT) === $solrDate);
        
        if (!$valid) {
            throw new \InvalidArgumentException(sprintf('invalid format of Solr date passed: "%s"', $solrDate));
        }
        
        return $dateTime;
    }
}
