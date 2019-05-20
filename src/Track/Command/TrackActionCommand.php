<?php


namespace App\Track\Command;


use Symfony\Component\Serializer\Annotation\{Groups, SerializedName};

/**
 * Class TrackActionCommand
 * @package App\Track\Command
 */
class TrackActionCommand
{

    /**
     * @var string
     * @Groups({"track"})
     */
    private $id;
    /**
     * @var string
     * @Groups({"track"})
     * @SerializedName("id_user")
     */
    private $idUser;
    /**
     * @var string
     * @Groups({"track"})
     * @SerializedName("source_label")
     */
    private $name;
    /**
     * @var \DateTimeImmutable
     * @Groups({"track"})
     * @SerializedName("date_created")
     */
    private $dateCreated;

    /**
     * TrackActionCommand constructor.
     * @param string $id
     * @param string $name
     * @param string $idUser
     * @param \DateTimeInterface $dateCreated
     */
    public function __construct(string $id, string $name, string $idUser, \DateTimeInterface $dateCreated)
    {

        $this->id = $id;
        $this->name = $name;
        $this->idUser = $idUser;
        $this->dateCreated = $dateCreated;

    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIdUser(): string
    {
        return $this->idUser;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDateCreated(): \DateTimeInterface
    {
        return $this->dateCreated;
    }
}
