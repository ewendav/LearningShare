<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 500)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'reportsGiven')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $ReportGiver = null;

    #[ORM\ManyToOne(inversedBy: 'reportsReceived')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $reportReceiver = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getReportGiver(): ?User
    {
        return $this->ReportGiver;
    }

    public function setReportGiver(?User $ReportGiver): static
    {
        $this->ReportGiver = $ReportGiver;

        return $this;
    }

    public function getReportReceiver(): ?User
    {
        return $this->reportReceiver;
    }

    public function setReportReceiver(?User $reportReceiver): static
    {
        $this->reportReceiver = $reportReceiver;

        return $this;
    }
}
