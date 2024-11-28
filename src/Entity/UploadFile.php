<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "upload_file")]
class UploadFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "bigint", unsigned: true)]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $url;

    #[ORM\Column(type: "string", length: 255)]
    private string $type;

    public function getId(): ?int 
    { 
        return $this->id; 
    }
    public function getUrl(): string 
    { 
        return $this->url; 
    }
    public function setUrl(string $url): self 
    { 
        $this->url = $url; 
        return $this; 
    }
    public function getType(): string 
    { 
        return $this->type; 
    }
    public function setType(string $type): self 
    { 
        $this->type = $type; 
        return $this; 
    }
}
