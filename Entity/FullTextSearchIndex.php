<?php

declare(strict_types=1);

namespace HoPeter1018\DoctrineFullTextSearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="HoPeter1018\DoctrineFullTextSearchBundle\Repository\FullTextSearchIndexRepository")
 * @ORM\Table(
 *     name="full_text_search_index",
 *     indexes={
 *         @ORM\Index(columns={"content"}, flags={"fulltext"}),
 *         @ORM\Index(columns={"model", "field"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(
 *             name="search_index",
 *             columns={
 *                 "model",
 *                 "field",
 *                 "foreign_id_int",
 *                 "foreign_id_guid",
 *                 "foreign_id_binary",
 *             }
 *         )
 *     }
 * )
 */
class FullTextSearchIndex
{
    /**
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(name="foreign_id_int", type="integer", nullable=true)
     */
    protected $foreignIdInt;

    /**
     * @var string
     *
     * @ORM\Column(name="foreign_id_guid", type="guid", nullable=true)
     */
    protected $foreignIdGuid;

    /**
     * @var mixed
     *
     * @ORM\Column(name="foreign_id_binary", type="binary", nullable=true)
     */
    protected $foreignIdBinary;

    /**
     * @var string
     *
     * @ORM\Column(name="model", type="string", length=150, nullable=false)
     */
    protected $model;

    /**
     * @var string
     *
     * @ORM\Column(name="field", type="string", length=90, nullable=false)
     */
    protected $field;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    protected $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * Get the value of Foreign Id Int.
     *
     * @return int
     */
    public function getForeignIdInt()
    {
        return $this->foreignIdInt;
    }

    /**
     * Set the value of Foreign Id Int.
     *
     * @param int $foreignIdInt
     *
     * @return self
     */
    public function setForeignIdInt($foreignIdInt)
    {
        $this->foreignIdInt = $foreignIdInt;

        return $this;
    }

    /**
     * Get the value of Foreign Id Guid.
     *
     * @return string
     */
    public function getForeignIdGuid()
    {
        return $this->foreignIdGuid;
    }

    /**
     * Set the value of Foreign Id Guid.
     *
     * @param string $foreignIdGuid
     *
     * @return self
     */
    public function setForeignIdGuid($foreignIdGuid)
    {
        $this->foreignIdGuid = $foreignIdGuid;

        return $this;
    }

    /**
     * Get the value of Foreign Id Binary.
     *
     * @return mixed
     */
    public function getForeignIdBinary()
    {
        return $this->foreignIdBinary;
    }

    /**
     * Set the value of Foreign Id Binary.
     *
     * @param mixed $foreignIdBinary
     *
     * @return self
     */
    public function setForeignIdBinary($foreignIdBinary)
    {
        $this->foreignIdBinary = $foreignIdBinary;

        return $this;
    }

    /**
     * Get the value of Model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set the value of Model.
     *
     * @param string $model
     *
     * @return self
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get the value of Field.
     *
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set the value of Field.
     *
     * @param string $field
     *
     * @return self
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get the value of Content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the value of Content.
     *
     * @param string $content
     *
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the value of Id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of Created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set the value of Created.
     *
     * @return self
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get the value of Updated.
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set the value of Updated.
     *
     * @return self
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;

        return $this;
    }
}
