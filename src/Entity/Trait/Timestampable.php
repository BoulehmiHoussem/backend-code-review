<?php
namespace App\Entity\Trait;
use Doctrine\ORM\Mapping as ORM;


trait Timestampable{

    /** Currently type-hinted as DateTime, but better to use immutable DateTimeImmutable 
     * With DateTime, this mutates the original timestamp stored in the entity, possibly causing bugs when persisting or comparing dates.
     * better to move it into trait 
    */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /**
     * get date ant time of creation of the entity
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Sets the createdAt before persistance
     */
    #[ORM\PrePersist]
    public function initializeCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

}