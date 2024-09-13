<?php

declare(strict_types=1);

/**
 * This file is part of the PHP-Pohoda-Connector package
 *
 * https://github.com/VitexSoftware/PHP-Pohoda-Connector
 *
 * (c) VitexSoftware. <https://vitexsoftware.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pohoda\Vyroba;

/**
 * Class representing VyrobaHeaderType.
 *
 * XSD Type: vyrobaHeaderType
 */
class VyrobaHeaderType
{
    /**
     * ID záznamu. Používá se při requestu.
     */
    private int $id = null;

    /**
     * Evidenční číslo dokladu.
     */
    private \Pohoda\Type\NumberType $number = null;

    /**
     * Datum vyskladnění (výdeje položek výrobku). Pokud hodnota není uvedena, použije se aktuální datum.
     */
    private \DateTime $date = null;

    /**
     * Čas vyskladnění (výdeje položek výrobku). Pokud hodnota není uvedena, použije se aktuální čas.
     */
    private \DateTime $time = null;

    /**
     * Datum výroby (příjmu výrobku). Pokud hodnota není uvedena, použije se datum vyskladnění.
     */
    private \DateTime $dateOfReceipt = null;

    /**
     * Čas výroby (příjmu výrobku). Pokud hodnota není uvedena, použije se čas vyskladnění.
     */
    private \DateTime $timeOfReceipt = null;

    /**
     * Párovací symbol pro párování dokladu v saldu.
     */
    private string $symPar = null;

    /**
     * Účet pro naskladnění výrobku na jiný výnosový účet.
     */
    private string $acc = null;

    /**
     * Text dokladu. Tento element je vyžadován při vytvoření dokladu.
     */
    private string $text = null;

    /**
     * Zákazníkova adresa.
     */
    private \Pohoda\Type\AddressType $partnerIdentity = null;

    /**
     * Zdrojové středisko.
     */
    private \Pohoda\Type\RefType $centreSource = null;

    /**
     * Cílové středisko.
     */
    private \Pohoda\Type\RefType $centreDestination = null;

    /**
     * Činnost.
     */
    private \Pohoda\Type\RefType $activity = null;

    /**
     * Zakázka.
     */
    private \Pohoda\Type\RefType $contract = null;

    /**
     * Poznámka.
     */
    private string $note = null;

    /**
     * Interní poznámka.
     */
    private string $intNote = null;

    /**
     * Zámek I (pouze verze E1). Doklady uzamčené prvním stupněm mohou editovat pouze uživatelé, kteří mají v agendě Přístupová práva nastaveno právo Editace záznamů uzamčených I. stupněm. Pouze pro export.
     */
    private string $lock1 = null;

    /**
     * Zámek II (pouze verze E1). Uzamčení dokladů pro uživatele s vyšším oprávněním. Takto uzamčené doklady nelze editovat. Pouze pro export.
     */
    private string $lock2 = null;

    /**
     * Označení záznamu v programu POHODA, sloupec "X". Výchozí hodnota je "označený záznam".
     */
    private string $markRecord = null;

    /**
     * Štítky záznamu.
     *
     * @var \Pohoda\Type\LabelType[]
     */
    private array $labels = null;

    /**
     * Podmíněná kontrola dokladu.
     */
    private \Pohoda\Type\TypeValidateType $validate = null;

    /**
     * Volitelný parametr.
     *
     * @var \Pohoda\Type\ParameterDocType[]
     */
    private array $parameters = null;

    /**
     * Gets as id.
     *
     * ID záznamu. Používá se při requestu.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets a new id.
     *
     * ID záznamu. Používá se při requestu.
     *
     * @param int $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets as number.
     *
     * Evidenční číslo dokladu.
     *
     * @return \Pohoda\Type\NumberType
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Sets a new number.
     *
     * Evidenční číslo dokladu.
     *
     * @return self
     */
    public function setNumber(?\Pohoda\Type\NumberType $number = null)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Gets as date.
     *
     * Datum vyskladnění (výdeje položek výrobku). Pokud hodnota není uvedena, použije se aktuální datum.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Sets a new date.
     *
     * Datum vyskladnění (výdeje položek výrobku). Pokud hodnota není uvedena, použije se aktuální datum.
     *
     * @return self
     */
    public function setDate(?\DateTime $date = null)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Gets as time.
     *
     * Čas vyskladnění (výdeje položek výrobku). Pokud hodnota není uvedena, použije se aktuální čas.
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Sets a new time.
     *
     * Čas vyskladnění (výdeje položek výrobku). Pokud hodnota není uvedena, použije se aktuální čas.
     *
     * @return self
     */
    public function setTime(?\DateTime $time = null)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Gets as dateOfReceipt.
     *
     * Datum výroby (příjmu výrobku). Pokud hodnota není uvedena, použije se datum vyskladnění.
     *
     * @return \DateTime
     */
    public function getDateOfReceipt()
    {
        return $this->dateOfReceipt;
    }

    /**
     * Sets a new dateOfReceipt.
     *
     * Datum výroby (příjmu výrobku). Pokud hodnota není uvedena, použije se datum vyskladnění.
     *
     * @return self
     */
    public function setDateOfReceipt(?\DateTime $dateOfReceipt = null)
    {
        $this->dateOfReceipt = $dateOfReceipt;

        return $this;
    }

    /**
     * Gets as timeOfReceipt.
     *
     * Čas výroby (příjmu výrobku). Pokud hodnota není uvedena, použije se čas vyskladnění.
     *
     * @return \DateTime
     */
    public function getTimeOfReceipt()
    {
        return $this->timeOfReceipt;
    }

    /**
     * Sets a new timeOfReceipt.
     *
     * Čas výroby (příjmu výrobku). Pokud hodnota není uvedena, použije se čas vyskladnění.
     *
     * @return self
     */
    public function setTimeOfReceipt(?\DateTime $timeOfReceipt = null)
    {
        $this->timeOfReceipt = $timeOfReceipt;

        return $this;
    }

    /**
     * Gets as symPar.
     *
     * Párovací symbol pro párování dokladu v saldu.
     *
     * @return string
     */
    public function getSymPar()
    {
        return $this->symPar;
    }

    /**
     * Sets a new symPar.
     *
     * Párovací symbol pro párování dokladu v saldu.
     *
     * @param string $symPar
     *
     * @return self
     */
    public function setSymPar($symPar)
    {
        $this->symPar = $symPar;

        return $this;
    }

    /**
     * Gets as acc.
     *
     * Účet pro naskladnění výrobku na jiný výnosový účet
     *
     * @return string
     */
    public function getAcc()
    {
        return $this->acc;
    }

    /**
     * Sets a new acc.
     *
     * Účet pro naskladnění výrobku na jiný výnosový účet
     *
     * @param string $acc
     *
     * @return self
     */
    public function setAcc($acc)
    {
        $this->acc = $acc;

        return $this;
    }

    /**
     * Gets as text.
     *
     * Text dokladu. Tento element je vyžadován při vytvoření dokladu.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Sets a new text.
     *
     * Text dokladu. Tento element je vyžadován při vytvoření dokladu.
     *
     * @param string $text
     *
     * @return self
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Gets as partnerIdentity.
     *
     * Zákazníkova adresa
     *
     * @return \Pohoda\Type\AddressType
     */
    public function getPartnerIdentity()
    {
        return $this->partnerIdentity;
    }

    /**
     * Sets a new partnerIdentity.
     *
     * Zákazníkova adresa
     *
     * @return self
     */
    public function setPartnerIdentity(?\Pohoda\Type\AddressType $partnerIdentity = null)
    {
        $this->partnerIdentity = $partnerIdentity;

        return $this;
    }

    /**
     * Gets as centreSource.
     *
     * Zdrojové středisko.
     *
     * @return \Pohoda\Type\RefType
     */
    public function getCentreSource()
    {
        return $this->centreSource;
    }

    /**
     * Sets a new centreSource.
     *
     * Zdrojové středisko.
     *
     * @return self
     */
    public function setCentreSource(?\Pohoda\Type\RefType $centreSource = null)
    {
        $this->centreSource = $centreSource;

        return $this;
    }

    /**
     * Gets as centreDestination.
     *
     * Cílové středisko.
     *
     * @return \Pohoda\Type\RefType
     */
    public function getCentreDestination()
    {
        return $this->centreDestination;
    }

    /**
     * Sets a new centreDestination.
     *
     * Cílové středisko.
     *
     * @return self
     */
    public function setCentreDestination(?\Pohoda\Type\RefType $centreDestination = null)
    {
        $this->centreDestination = $centreDestination;

        return $this;
    }

    /**
     * Gets as activity.
     *
     * Činnost.
     *
     * @return \Pohoda\Type\RefType
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * Sets a new activity.
     *
     * Činnost.
     *
     * @return self
     */
    public function setActivity(?\Pohoda\Type\RefType $activity = null)
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * Gets as contract.
     *
     * Zakázka.
     *
     * @return \Pohoda\Type\RefType
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * Sets a new contract.
     *
     * Zakázka.
     *
     * @return self
     */
    public function setContract(?\Pohoda\Type\RefType $contract = null)
    {
        $this->contract = $contract;

        return $this;
    }

    /**
     * Gets as note.
     *
     * Poznámka.
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Sets a new note.
     *
     * Poznámka.
     *
     * @param string $note
     *
     * @return self
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Gets as intNote.
     *
     * Interní poznámka.
     *
     * @return string
     */
    public function getIntNote()
    {
        return $this->intNote;
    }

    /**
     * Sets a new intNote.
     *
     * Interní poznámka.
     *
     * @param string $intNote
     *
     * @return self
     */
    public function setIntNote($intNote)
    {
        $this->intNote = $intNote;

        return $this;
    }

    /**
     * Gets as lock1.
     *
     * Zámek I (pouze verze E1). Doklady uzamčené prvním stupněm mohou editovat pouze uživatelé, kteří mají v agendě Přístupová práva nastaveno právo Editace záznamů uzamčených I. stupněm. Pouze pro export.
     *
     * @return string
     */
    public function getLock1()
    {
        return $this->lock1;
    }

    /**
     * Sets a new lock1.
     *
     * Zámek I (pouze verze E1). Doklady uzamčené prvním stupněm mohou editovat pouze uživatelé, kteří mají v agendě Přístupová práva nastaveno právo Editace záznamů uzamčených I. stupněm. Pouze pro export.
     *
     * @param string $lock1
     *
     * @return self
     */
    public function setLock1($lock1)
    {
        $this->lock1 = $lock1;

        return $this;
    }

    /**
     * Gets as lock2.
     *
     * Zámek II (pouze verze E1). Uzamčení dokladů pro uživatele s vyšším oprávněním. Takto uzamčené doklady nelze editovat. Pouze pro export.
     *
     * @return string
     */
    public function getLock2()
    {
        return $this->lock2;
    }

    /**
     * Sets a new lock2.
     *
     * Zámek II (pouze verze E1). Uzamčení dokladů pro uživatele s vyšším oprávněním. Takto uzamčené doklady nelze editovat. Pouze pro export.
     *
     * @param string $lock2
     *
     * @return self
     */
    public function setLock2($lock2)
    {
        $this->lock2 = $lock2;

        return $this;
    }

    /**
     * Gets as markRecord.
     *
     * Označení záznamu v programu POHODA, sloupec "X". Výchozí hodnota je "označený záznam".
     *
     * @return string
     */
    public function getMarkRecord()
    {
        return $this->markRecord;
    }

    /**
     * Sets a new markRecord.
     *
     * Označení záznamu v programu POHODA, sloupec "X". Výchozí hodnota je "označený záznam".
     *
     * @param string $markRecord
     *
     * @return self
     */
    public function setMarkRecord($markRecord)
    {
        $this->markRecord = $markRecord;

        return $this;
    }

    /**
     * Adds as label.
     *
     * Štítky záznamu.
     *
     * @return self
     */
    public function addToLabels(\Pohoda\Type\LabelType $label)
    {
        $this->labels[] = $label;

        return $this;
    }

    /**
     * isset labels.
     *
     * Štítky záznamu.
     *
     * @param int|string $index
     *
     * @return bool
     */
    public function issetLabels($index)
    {
        return isset($this->labels[$index]);
    }

    /**
     * unset labels.
     *
     * Štítky záznamu.
     *
     * @param int|string $index
     */
    public function unsetLabels($index): void
    {
        unset($this->labels[$index]);
    }

    /**
     * Gets as labels.
     *
     * Štítky záznamu.
     *
     * @return \Pohoda\Type\LabelType[]
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * Sets a new labels.
     *
     * Štítky záznamu.
     *
     * @param \Pohoda\Type\LabelType[] $labels
     *
     * @return self
     */
    public function setLabels(?array $labels = null)
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * Gets as validate.
     *
     * Podmíněná kontrola dokladu.
     *
     * @return \Pohoda\Type\TypeValidateType
     */
    public function getValidate()
    {
        return $this->validate;
    }

    /**
     * Sets a new validate.
     *
     * Podmíněná kontrola dokladu.
     *
     * @return self
     */
    public function setValidate(?\Pohoda\Type\TypeValidateType $validate = null)
    {
        $this->validate = $validate;

        return $this;
    }

    /**
     * Adds as parameter.
     *
     * Volitelný parametr.
     *
     * @return self
     */
    public function addToParameters(\Pohoda\Type\ParameterDocType $parameter)
    {
        $this->parameters[] = $parameter;

        return $this;
    }

    /**
     * isset parameters.
     *
     * Volitelný parametr.
     *
     * @param int|string $index
     *
     * @return bool
     */
    public function issetParameters($index)
    {
        return isset($this->parameters[$index]);
    }

    /**
     * unset parameters.
     *
     * Volitelný parametr.
     *
     * @param int|string $index
     */
    public function unsetParameters($index): void
    {
        unset($this->parameters[$index]);
    }

    /**
     * Gets as parameters.
     *
     * Volitelný parametr.
     *
     * @return \Pohoda\Type\ParameterDocType[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Sets a new parameters.
     *
     * Volitelný parametr.
     *
     * @param \Pohoda\Type\ParameterDocType[] $parameters
     *
     * @return self
     */
    public function setParameters(?array $parameters = null)
    {
        $this->parameters = $parameters;

        return $this;
    }
}
