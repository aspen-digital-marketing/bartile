<?php
/**
 * GetExtendedContactDetails
 *
 * PHP version 5
 *
 * @category Class
 * @package  SendinBlue\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * SendinBlue API
 *
 * SendinBlue provide a RESTFul API that can be used with any languages. With this API, you will be able to :   - Manage your campaigns and get the statistics   - Manage your contacts   - Send transactional Emails and SMS   - and much more...  You can download our wrappers at https://github.com/orgs/sendinblue  **Possible responses**   | Code | Message |   | :-------------: | ------------- |   | 200  | OK. Successful Request  |   | 201  | OK. Successful Creation |   | 202  | OK. Request accepted |   | 204  | OK. Successful Update/Deletion  |   | 400  | Error. Bad Request  |   | 401  | Error. Authentication Needed  |   | 402  | Error. Not enough credit, plan upgrade needed  |   | 403  | Error. Permission denied  |   | 404  | Error. Object does not exist |   | 405  | Error. Method not allowed  |   | 406  | Error. Not Acceptable  |
 *
 * OpenAPI spec version: 3.0.0
 * Contact: contact@sendinblue.com
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 2.4.29
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace SendinBlue\Client\Model;

use \ArrayAccess;
use \SendinBlue\Client\ObjectSerializer;

/**
 * GetExtendedContactDetails Class Doc Comment
 *
 * @category Class
 * @package  SendinBlue\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class GetExtendedContactDetails implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'getExtendedContactDetails';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'email' => 'string',
        'id' => 'int',
        'emailBlacklisted' => 'bool',
        'smsBlacklisted' => 'bool',
        'createdAt' => 'string',
        'modifiedAt' => 'string',
        'listIds' => 'int[]',
        'listUnsubscribed' => 'int[]',
        'attributes' => 'object',
        'statistics' => '\SendinBlue\Client\Model\GetExtendedContactDetailsStatistics'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'email' => 'email',
        'id' => 'int64',
        'emailBlacklisted' => null,
        'smsBlacklisted' => null,
        'createdAt' => null,
        'modifiedAt' => null,
        'listIds' => 'int64',
        'listUnsubscribed' => 'int64',
        'attributes' => null,
        'statistics' => null
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'email' => 'email',
        'id' => 'id',
        'emailBlacklisted' => 'emailBlacklisted',
        'smsBlacklisted' => 'smsBlacklisted',
        'createdAt' => 'createdAt',
        'modifiedAt' => 'modifiedAt',
        'listIds' => 'listIds',
        'listUnsubscribed' => 'listUnsubscribed',
        'attributes' => 'attributes',
        'statistics' => 'statistics'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'email' => 'setEmail',
        'id' => 'setId',
        'emailBlacklisted' => 'setEmailBlacklisted',
        'smsBlacklisted' => 'setSmsBlacklisted',
        'createdAt' => 'setCreatedAt',
        'modifiedAt' => 'setModifiedAt',
        'listIds' => 'setListIds',
        'listUnsubscribed' => 'setListUnsubscribed',
        'attributes' => 'setAttributes',
        'statistics' => 'setStatistics'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'email' => 'getEmail',
        'id' => 'getId',
        'emailBlacklisted' => 'getEmailBlacklisted',
        'smsBlacklisted' => 'getSmsBlacklisted',
        'createdAt' => 'getCreatedAt',
        'modifiedAt' => 'getModifiedAt',
        'listIds' => 'getListIds',
        'listUnsubscribed' => 'getListUnsubscribed',
        'attributes' => 'getAttributes',
        'statistics' => 'getStatistics'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$swaggerModelName;
    }

    

    


    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['email'] = isset($data['email']) ? $data['email'] : null;
        $this->container['id'] = isset($data['id']) ? $data['id'] : null;
        $this->container['emailBlacklisted'] = isset($data['emailBlacklisted']) ? $data['emailBlacklisted'] : null;
        $this->container['smsBlacklisted'] = isset($data['smsBlacklisted']) ? $data['smsBlacklisted'] : null;
        $this->container['createdAt'] = isset($data['createdAt']) ? $data['createdAt'] : null;
        $this->container['modifiedAt'] = isset($data['modifiedAt']) ? $data['modifiedAt'] : null;
        $this->container['listIds'] = isset($data['listIds']) ? $data['listIds'] : null;
        $this->container['listUnsubscribed'] = isset($data['listUnsubscribed']) ? $data['listUnsubscribed'] : null;
        $this->container['attributes'] = isset($data['attributes']) ? $data['attributes'] : null;
        $this->container['statistics'] = isset($data['statistics']) ? $data['statistics'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['email'] === null) {
            $invalidProperties[] = "'email' can't be null";
        }
        if ($this->container['id'] === null) {
            $invalidProperties[] = "'id' can't be null";
        }
        if ($this->container['emailBlacklisted'] === null) {
            $invalidProperties[] = "'emailBlacklisted' can't be null";
        }
        if ($this->container['smsBlacklisted'] === null) {
            $invalidProperties[] = "'smsBlacklisted' can't be null";
        }
        if ($this->container['createdAt'] === null) {
            $invalidProperties[] = "'createdAt' can't be null";
        }
        if ($this->container['modifiedAt'] === null) {
            $invalidProperties[] = "'modifiedAt' can't be null";
        }
        if ($this->container['listIds'] === null) {
            $invalidProperties[] = "'listIds' can't be null";
        }
        if ($this->container['attributes'] === null) {
            $invalidProperties[] = "'attributes' can't be null";
        }
        if ($this->container['statistics'] === null) {
            $invalidProperties[] = "'statistics' can't be null";
        }
        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->container['email'];
    }

    /**
     * Sets email
     *
     * @param string $email Email address of the contact for which you requested the details
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->container['email'] = $email;

        return $this;
    }

    /**
     * Gets id
     *
     * @return int
     */
    public function getId()
    {
        return $this->container['id'];
    }

    /**
     * Sets id
     *
     * @param int $id ID of the contact for which you requested the details
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->container['id'] = $id;

        return $this;
    }

    /**
     * Gets emailBlacklisted
     *
     * @return bool
     */
    public function getEmailBlacklisted()
    {
        return $this->container['emailBlacklisted'];
    }

    /**
     * Sets emailBlacklisted
     *
     * @param bool $emailBlacklisted Blacklist status for email campaigns (true=blacklisted, false=not blacklisted)
     *
     * @return $this
     */
    public function setEmailBlacklisted($emailBlacklisted)
    {
        $this->container['emailBlacklisted'] = $emailBlacklisted;

        return $this;
    }

    /**
     * Gets smsBlacklisted
     *
     * @return bool
     */
    public function getSmsBlacklisted()
    {
        return $this->container['smsBlacklisted'];
    }

    /**
     * Sets smsBlacklisted
     *
     * @param bool $smsBlacklisted Blacklist status for SMS campaigns (true=blacklisted, false=not blacklisted)
     *
     * @return $this
     */
    public function setSmsBlacklisted($smsBlacklisted)
    {
        $this->container['smsBlacklisted'] = $smsBlacklisted;

        return $this;
    }

    /**
     * Gets createdAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->container['createdAt'];
    }

    /**
     * Sets createdAt
     *
     * @param string $createdAt Creation UTC date-time of the contact (YYYY-MM-DDTHH:mm:ss.SSSZ)
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->container['createdAt'] = $createdAt;

        return $this;
    }

    /**
     * Gets modifiedAt
     *
     * @return string
     */
    public function getModifiedAt()
    {
        return $this->container['modifiedAt'];
    }

    /**
     * Sets modifiedAt
     *
     * @param string $modifiedAt Last modification UTC date-time of the contact (YYYY-MM-DDTHH:mm:ss.SSSZ)
     *
     * @return $this
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->container['modifiedAt'] = $modifiedAt;

        return $this;
    }

    /**
     * Gets listIds
     *
     * @return int[]
     */
    public function getListIds()
    {
        return $this->container['listIds'];
    }

    /**
     * Sets listIds
     *
     * @param int[] $listIds listIds
     *
     * @return $this
     */
    public function setListIds($listIds)
    {
        $this->container['listIds'] = $listIds;

        return $this;
    }

    /**
     * Gets listUnsubscribed
     *
     * @return int[]
     */
    public function getListUnsubscribed()
    {
        return $this->container['listUnsubscribed'];
    }

    /**
     * Sets listUnsubscribed
     *
     * @param int[] $listUnsubscribed listUnsubscribed
     *
     * @return $this
     */
    public function setListUnsubscribed($listUnsubscribed)
    {
        $this->container['listUnsubscribed'] = $listUnsubscribed;

        return $this;
    }

    /**
     * Gets attributes
     *
     * @return object
     */
    public function getAttributes()
    {
        return $this->container['attributes'];
    }

    /**
     * Sets attributes
     *
     * @param object $attributes Set of attributes of the contact
     *
     * @return $this
     */
    public function setAttributes($attributes)
    {
        $this->container['attributes'] = $attributes;

        return $this;
    }

    /**
     * Gets statistics
     *
     * @return \SendinBlue\Client\Model\GetExtendedContactDetailsStatistics
     */
    public function getStatistics()
    {
        return $this->container['statistics'];
    }

    /**
     * Sets statistics
     *
     * @param \SendinBlue\Client\Model\GetExtendedContactDetailsStatistics $statistics statistics
     *
     * @return $this
     */
    public function setStatistics($statistics)
    {
        $this->container['statistics'] = $statistics;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }

        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}


