<?php


namespace App\Form\DTO;


/**
 * Class UserData
 * @package App\Form\DTO
 */
class UserData
{
    /**
     * @var string|null
     */
    private $id;
    /**
     * @var string|null
     */
    private $firstname;

    /**
     * @var string|null
     */
    private $lastname;

    /**
     * @var string|null
     */
    private $username;

    /**
     * @var \DateTimeImmutable|null
     */
    private $birthday;

    /**
     * @var string|null
     */
    private $password;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return UserData
     */
    public function setId(?string $id): UserData
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     * @return UserData
     */
    public function setFirstname(?string $firstname): UserData
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     * @return UserData
     */
    public function setLastname(?string $lastname): UserData
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     * @return UserData
     */
    public function setUsername(?string $username): UserData
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getBirthday(): ?\DateTimeImmutable
    {
        return $this->birthday;
    }

    /**
     * @param \DateTimeImmutable|null $birthday
     * @return UserData
     */
    public function setBirthday(?\DateTimeImmutable $birthday): UserData
    {
        $this->birthday = $birthday;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return UserData
     */
    public function setPassword(?string $password): UserData
    {
        $this->password = $password;
        return $this;
    }
}
