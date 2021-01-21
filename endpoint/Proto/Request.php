<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: task.proto

namespace Proto;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>proto.Request</code>
 */
class Request extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string id = 1;</code>
     */
    protected $id = '';
    /**
     * Generated from protobuf field <code>int64 execTime = 2;</code>
     */
    protected $execTime = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $id
     *     @type int|string $execTime
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Task::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string id = 1;</code>
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Generated from protobuf field <code>string id = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setId($var)
    {
        GPBUtil::checkString($var, True);
        $this->id = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 execTime = 2;</code>
     * @return int|string
     */
    public function getExecTime()
    {
        return $this->execTime;
    }

    /**
     * Generated from protobuf field <code>int64 execTime = 2;</code>
     * @param int|string $var
     * @return $this
     */
    public function setExecTime($var)
    {
        GPBUtil::checkInt64($var);
        $this->execTime = $var;

        return $this;
    }

}

