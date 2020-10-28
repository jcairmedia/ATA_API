<?php

namespace App\Utils\CustomMailer;

/**
 * EmailData.
 *
 * Encapsulamiento de propiedades del correo electrónico
 */
class EmailData
{
    private $objFrom;
    private $arrayTO;
    private $stringSubject;
    private $stringPlainBody;
    private $stringHtmlBody = null;
    private $arrayAttachments = [];
    private $arrayCC = [];
    private $arrayBCC = [];
    private $objReplyTo = null;

    /**
     * Constructor.
     */
    public function __construct($objFrom, $arrayTO, $stringSubject, $stringPlainBody, $stringHtmlBody = null, $arrayAttachments = [], $arrayCC = [], $arrayBCC = [], $objReplyTo = null)
    {
        $this->objFrom = $objFrom;
        $this->arrayTO = $arrayTO;
        $this->stringSubject = $stringSubject;
        $this->stringPlainBody = $stringPlainBody;
        $this->stringHtmlBody = $stringHtmlBody;
        $this->arrayAttachments = $arrayAttachments;
        $this->arrayCC = $arrayCC;
        $this->arrayBCC = $arrayBCC;
        $this->objReplyTo = $objReplyTo;
    }

    //# Getter's

    /**
     * getFrom.
     */
    public function getFrom()
    {
        return $this->objFrom;
    }

    /**
     * getTO.
     */
    public function getTO()
    {
        return $this->arrayTO;
    }

    /**
     * getSubject.
     */
    public function getSubject()
    {
        return $this->stringSubject;
    }

    /**
     * getPlainBody.
     */
    public function getPlainBody()
    {
        return $this->stringPlainBody;
    }

    /**
     * getHtmlBody.
     */
    public function getHtmlBody()
    {
        return $this->stringHtmlBody;
    }

    /**
     * getAttachments.
     */
    public function getAttachments()
    {
        return $this->arrayAttachments;
    }

    /**
     * getCC.
     */
    public function getCC()
    {
        return $this->arrayCC;
    }

    /**
     * getBCC.
     */
    public function getBCC()
    {
        return $this->arrayBCC;
    }

    /**
     * getReplyTo.
     */
    public function getReplyTo()
    {
        return $this->objReplyTo;
    }
}
