<?php

namespace mholt\txtAHolic\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Nonce
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Nonce
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
     * @ORM\Column(name="nonce", type="string", length=255)
     */
    private $nonce;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;


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
     * Set nonce
     *
     * @param string $nonce
     * @return Nonce
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;

        return $this;
    }

    /**
     * Get nonce
     *
     * @return string 
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     * @return Nonce
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }
	
	public function isExpired()
	{
		$timestamp = $this->getTime()->getTimestamp();
		$validFrom = strtotime('-5 minutes');
		$validTo = strtotime('+5 minutes');
		
		return $timestamp < $validFrom || $timestamp > $validTo;
	}
}
