<?php

namespace Osen\Glovo\Models;

class Order implements \JsonSerializable
{

  /**
   * The order will be activated on `scheduleTime`
   */
  const STATE_SCHEDULED = "SCHEDULED";

  /**
   * The order is either being delivered or about to be
   */
  const STATE_ACTIVE = "ACTIVE";

  /**
   * The delivery has finished successfully
   */
  const STATE_DELIVERED = "DELIVERED";

  /**
   * The order is canceled and it wont be delivered
   */
  const STATE_CANCELED = "CANCELED";

  /**
   * Id of the order
   *
   * @var int
   */
  private $id;

  /**
   * Description detailing the package to be delivered
   *
   * @var string
   */
  private $description;

  /**
   * Order creation time
   *
   * @var \DateTime
   */
  private $creationTime;

  /**
   * Scheduled activation time of the order. Optional
   *
   * @var \DateTime
   */
  private $scheduleTime;

  /**
   * Ordered list of addresses (pickups and deliveries) of the order. Usually your orders will have one PICKUP address and one DELIVERY address
   *
   * @var Address[]
   */
  private $addresses;

  /**
   * Current state of the order (one of SCHEDULED, ACTIVE, DELIVERED, CANCELED)
   *
   * @var string
   */
  private $state;

  public function jsonSerialize()
  {
    $data = [];

    if (!is_null($this->scheduleTime)) {
      $data['scheduleTime'] = $this->scheduleTime->getTimestamp();
    }

    if (!is_null($this->description)) {
      $data['description'] = $this->description;
    }

    if (!is_null($this->addresses)) {
      $data['addresses'] = $this->addresses;
    }

    return $data;
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }

  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }

  /**
   * @return \DateTime
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }

  /**
   * @param \DateTime $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }

  /**
   * @return \DateTime
   */
  public function getScheduleTime()
  {
    return $this->scheduleTime;
  }

  /**
   * @param \DateTime $scheduleTime
   */
  public function setScheduleTime($scheduleTime)
  {
    $this->scheduleTime = $scheduleTime;
  }

  /**
   * @return Address[]
   */
  public function getAddresses()
  {
    return $this->addresses;
  }

  /**
   * @param Address[] $addresses
   */
  public function setAddresses($addresses)
  {
    $this->addresses = $addresses;
  }

  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }

  /**
   * @param string $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
}
