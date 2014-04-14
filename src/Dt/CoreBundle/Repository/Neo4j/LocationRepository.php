<?php

namespace Dt\CoreBundle\Repository\Neo4j;


use HireVoice\Neo4j\Repository as BaseRepository;

use Dt\CoreBundle\Entity\Neo4j\Location;
/**
 * Description of LocationRepository
 *
 * @author Kevin
 */
class LocationRepository extends BaseRepository
{
    
    public function findShortestRouteTo(Location $from,Location $to)
    {
        
    }
}
