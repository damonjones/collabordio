<?php

namespace HT\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Party
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Party
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\Column(name="tracks", type="array")
     */
    private $tracks;

    public function __construct()
    {
        $this->code = uniqid();
        $this->tracks = array();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Party
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Party
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set tracks
     *
     * @param array $tracks
     * @return Party
     */
    public function setTracks($tracks)
    {
        $this->tracks = $tracks;
    
        return $this;
    }

    /**
     * Get tracks
     *
     * @return array 
     */
    public function getTracks()
    {
        return $this->tracks ?: array();
    }

    public function addTrack($key, $name, $artist, $album)
    {
        if (is_array($this->tracks)) {
            foreach ($this->tracks as $track) {
                if ($key == $track['key']) {
                    return;
                }
            }
        }

        $this->tracks[] = array(
            'key' => $key,
            'name' => $name,
            'artist' => $artist,
            'album' => $album
        );
    }
}
