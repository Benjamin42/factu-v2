<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Bdl
 *
 * @ORM\Table("Bdl)
 * @ORM\Entity(repositoryClass="App\Repository\BdlRepository")
 */
class Bdl
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
    * @ORM\ManyToOne(targetEntity="App\Entity\Client")
    * @ORM\JoinColumn(nullable=false)
    */
    private $client;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_bdl", type="integer")
     */
    private $numBdl;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_bdl", type="datetime")
     */
    private $dateBdl;

    /**
     * @var boolean
     *
     * @ORM\Column(name="to_delivered", type="boolean")
     */
    private $toDelivered;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_delivered", type="boolean")
     */
    private $isDelivered;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_delivered", type="datetime", nullable=true)
     */
    private $dateDelivered;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\CommandeProduct", mappedBy="bdl", cascade={"persist", "remove"}, orphanRemoval=true)
    */
    protected $commandeProducts;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\CommandeService", mappedBy="bdl", cascade={"persist", "remove"}, orphanRemoval=true)
    */
    protected $commandeServices;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\Commande", mappedBy="bdl")
    */
    protected $commandes;

    public function __construct()
    {
        $this->commandeProducts = new ArrayCollection();
        $this->commandeServices = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        
        $this->dateBdl = new \Datetime();
    }

    public function formatedLabel() {
        return $this->numBdl . " - " . $this->client->formatedLabel() . " - " . $this->title;
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
     * Set numBdl
     *
     * @param integer $numBdl
     * @return Bdl
     */
    public function setNumBdl($numBdl)
    {
        $this->numBdl = $numBdl;

        return $this;
    }

    /**
     * Get numBdl
     *
     * @return integer 
     */
    public function getNumBdl()
    {
        return $this->numBdl;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Bdl
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set dateBdl
     *
     * @param \DateTime $dateBdl
     * @return Bdl
     */
    public function setDateBdl($dateBdl)
    {
        $this->dateBdl = $dateBdl;

        return $this;
    }

    /**
     * Get dateBdl
     *
     * @return \DateTime 
     */
    public function getDateBdl()
    {
        return $this->dateBdl;
    }

    /**
     * Set toDelivered
     *
     * @param boolean $toDelivered
     * @return Commande
     */
    public function setToDelivered($toDelivered)
    {
        $this->toDelivered = $toDelivered;

        return $this;
    }

    /**
     * Get toDelivered
     *
     * @return boolean 
     */
    public function getToDelivered()
    {
        return $this->toDelivered;
    }

    /**
     * Set isDelivered
     *
     * @param boolean $isDelivered
     * @return Commande
     */
    public function setIsDelivered($isDelivered)
    {
        $this->isDelivered = $isDelivered;

        return $this;
    }

    /**
     * Get isDelivered
     *
     * @return boolean 
     */
    public function getIsDelivered()
    {
        return $this->isDelivered;
    }

    /**
     * Set dateDelivered
     *
     * @param \DateTime $dateDelivered
     * @return Commande
     */
    public function setDateDelivered($dateDelivered)
    {
        $this->dateDelivered = $dateDelivered;

        return $this;
    }

    /**
     * Get dateDelivered
     *
     * @return \DateTime 
     */
    public function getDateDelivered()
    {
        return $this->dateDelivered;
    }

    /***************************************************************************
    *                               CLIENT
    ****************************************************************************/

    /**
     * Set client
     *
     * @param App\Entity\Client $client
     * @return Commande
     */
    public function setClient(\App\Entity\Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \App\Entity\Client 
     */
    public function getClient()
    {
        return $this->client;
    }

    /***************************************************************************
    *                               COMMANDE PRODUCT
    ****************************************************************************/

    /**
     * Add commandeProducts
     *
     * @param \App\Entity\CommandeProduct $commandeProducts
     * @return Commande
     */
    public function addCommandeProduct(\App\Entity\CommandeProduct $commandeProducts)
    {
        $this->commandeProducts[] = $commandeProducts;
        $commandeProducts->setBdl($this);
        return $this;
    }

    /**
     * Remove commandeProducts
     *
     * @param \App\Entity\CommandeProduct $commandeProducts
     */
    public function removeCommandeProduct(\App\Entity\CommandeProduct $commandeProduct)
    {
        $this->commandeProducts->removeElement($commandeProduct);
        $commandeProduct->setBdl(null);
    }

    /**
     * Get commandeProducts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCommandeProducts()
    {
        return $this->commandeProducts;
    }

    /***************************************************************************
    *                               COMMANDE SERVICE
    ****************************************************************************/

    /**
     * Add commandeServices
     *
     * @param \App\Entity\CommandeService $commandeService
     * @return Commande
     */
    public function addCommandeService(\App\Entity\CommandeService $commandeService)
    {
        $this->commandeServices[] = $commandeService;
        $commandeService->setBdl($this);
        return $this;
    }

    /**
     * Remove commandeService
     *
     * @param \App\Entity\CommandeService $commandeService
     */
    public function removeCommandeService(\App\Entity\CommandeService $commandeService)
    {
        $this->commandeServices->removeElement($commandeService);
        $commandeService->setBdl(null);
    }

    /**
     * Get commandeServices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCommandeServices()
    {
        return $this->commandeServices;
    }

    /***************************************************************************
    *                               COMMANDE 
    ****************************************************************************/

    /**
     * Add commande
     *
     * @param \App\Entity\CommandeService $commande
     * @return Commande
     */
    public function addCommande(\App\Entity\Commande $commande)
    {
        $this->commande[] = $commande;
        $commande->setBdl($this);
        return $this;
    }

    /**
     * Remove commande
     *
     * @param \App\Entity\Commande $commande
     */
    public function removeCommande(\App\Entity\Commande $commande)
    {
        $this->commande->removeElement($commande);
        $commandeService->setBdl(null);
    }

    /**
     * Get commandes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCommandes()
    {
        return $this->commandes;
    }

    public function calcMontantTTC() {
        $amt = 0;
        foreach ($this->commandeProducts as $commandeProduct) {
            $amt += $commandeProduct->calcMontantTTC();
        }
        foreach ($this->commandeServices as $commandeService) {
            $amt += $commandeService->calcMontantTTC();
        }
        return $amt;
    }
    
}
