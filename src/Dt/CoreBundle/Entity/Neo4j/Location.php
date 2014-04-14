<?php

namespace Dt\CoreBundle\Entity\Neo4j;

use \HireVoice\Neo4j\Annotation as OGM;


/**
 * Entity location (neo4j entity)
 *
 * @author Kevin
 * @OGM\Entity(repositoryClass="Dt\CoreBundle\Repository\Neo4j\LocationRepository", labels="Location")
 */
class Location {
    
    /**
     * The internal node ID from Neo4j must be stored. Thus an Auto field is required
     * @OGM\Auto
     */
    protected $id;
    
    
    /**
     * @OGM\Property
     */
    protected $name;
    
    public function getId() 
    {
        return $this->id;
    }

    public function getName() 
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
}
