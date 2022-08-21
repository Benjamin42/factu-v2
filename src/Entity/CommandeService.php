<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CommandeService
 *
 * @ORM\Table("CommandeService")
 * @ORM\Entity(repositoryClass="App\Repository\CommandeServiceRepository")
 */
class CommandeService
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
    * @ORM\ManyToOne(targetEntity="App\Entity\Commande", inversedBy="commandeServices")
    * @ORM\JoinColumn(name="commande_id", referencedColumnName="id", nullable=true)
    */
    private $commande;

    /**
    * @ORM\ManyToOne(targetEntity="App\Entity\Bdl", inversedBy="commandeServices")
    * @ORM\JoinColumn(name="bdl_id", referencedColumnName="id", nullable=true)
    */
    private $bdl;

    /**
    * @ORM\ManyToOne(targetEntity="App\Entity\Service")
    * @ORM\JoinColumn(nullable=false)
    */
    private $service;

    /**
     * @var float
     *
     * @ORM\Column(name="amt", type="float")
     */
    private $amt;


    public function __construct()
    {
        $this->amt = 0.0;
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
     * Set amt
     *
     * @param float $amt
     * @return CommandeService
     */
    public function setAmt($amt)
    {
        $this->amt = $amt;

        return $this;
    }

    /**
     * Get amt
     *
     * @return float 
     */
    public function getAmt()
    {
        return $this->amt;
    }

    /***************************************************************************
    *                               COMMANDE
    ****************************************************************************/

    /**
     * Set commande
     *
     * @param \App\Entity\Commande $commande
     * @return CommandeService
     */
    public function setCommande($commande)
    {
        $this->commande = $commande;

        return $this;
    }

    /**
     * Get commande
     *
     * @return \App\Entity\Commande 
     */
    public function getCommande()
    {
        return $this->commande;
    }

    /***************************************************************************
    *                               BDL
    ****************************************************************************/

    /**
     * Set bdl
     *
     * @param \App\Entity\Bdl $bdl
     * @return CommandeService
     */
    public function setBdl($bdl)
    {
        $this->bdl = $bdl;

        return $this;
    }

    /**
     * Get bdl
     *
     * @return \App\Entity\Bdl
     */
    public function getBdl()
    {
        return $this->bdl;
    }

    /**
     * Set service
     *
     * @param \App\Entity\Service $service
     * @return CommandeService
     */
    public function setService(\App\Entity\Service $service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return \App\Entity\Service 
     */
    public function getService()
    {
        return $this->service;
    }

    public function calcMontantTTC() {
        return $this->amt;
    }
}
