<?php

/**
 * DotCoreEventBLL - Contains the business logic behind the events
 *
 * @author perrin
 */
class DotCoreEventBLL extends DotCoreContentBLL {


    /*
     *
     * Abstract Methods Implementation:
     *
     */

    /**
     *
     * @return DotCoreEventDAL
     */
    public static function GetDAL() {
        return self::GetDALHelper('DotCoreEventDAL');
    }

    /*
     *
     * Fields accessors:
     *
     */

    /**
     * Gets the auto incrementing ID of this DAL
     * @return DotCoreAutoIncrementingKey
     */
    public function getFieldEventID()
    {
        return $this->GetDAL()->GetField(DotCoreEventDAL::EVENTS_ID);
    }

    /**
     * Gets the field that defines the title of the event
     * @return DotCoreStringField
     */
    public function getFieldTitle()
    {
        return $this->GetDAL()->GetField(DotCoreEventDAL::EVENTS_TITLE);
    }

    /**
     * Gets the field that defines the description of the event
     * @return DotCoreStringField
     */
    public function getFieldDescription()
    {
        return $this->GetDAL()->GetField(DotCoreEventDAL::EVENTS_DESCRIPTION);
    }

    /**
     * Gets the field that defines the details of the events
     * @return DotCoreStringField
     */
    public function getFieldDetails()
    {
        return $this->GetDAL()->GetField(DotCoreEventDAL::EVENTS_DETAILS);
    }

    /**
     * Gets the field that defines the date of the event
     * @return DotCoreStringField
     */
    public function getFieldDate()
    {
        return $this->GetDAL()->GetField(DotCoreEventDAL::EVENTS_DATE);
    }

    /**
     * Gets the field that defines the language of the event
     * @return DotCoreIntField
     */
    public function getFieldEventLanguageID()
    {
        return $this->GetDAL()->GetField(DotCoreEventDAL::EVENTS_LANGUAGE_ID);
    }

    /*
     *
     * Fulltext accessors
     *
     */

     /**
      * Returns the only fulltext this DAL has
      * @return DotCoreDALFulltext
      */
     public function getSearchFulltext()
     {
         return $this->GetDAL()->GetFulltext(DotCoreEventDAL::EVENTS_FULLTEXT);
     }

    /*
     *
     * Busines Logic Methods:
     *
     */

    /**
     *
     * @param int $event_id
     * @return DotCoreEventBLL
     */
    public function ByEventID($event_id) {
        $restraint = new DotCoreDALRestraint();
        $restraint->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldEventID(), $event_id));

        return $this
            ->Restraints($restraint);
    }

    /**
     *
     * @param int $lang_id
     * @return DotCoreEventBLL
     */
    public function ByEventLanguageID($lang_id) {
        $restraint = new DotCoreDALRestraint();
        $restraint->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldEventLanguageID(), $lang_id));
        
        return $this
            ->Restraints($restraint);
    }

    /**
     *
     * @param int $year
     * @param int $month
     * @return DotCoreEventBLL
     */
    public function ByMonth($year, $month) {
        $restraint = new DotCoreDALRestraint();
        $restraint->AddRestraint(
            new DotCoreEntityRestraint(
                new DotCoreDateComponentEntity($this->getFieldDate(), DotCoreDateComponentEntity::DATE_COMPONENT_YEAR),
                $year
            )
        )
        ->AddRestraint(
            new DotCoreEntityRestraint(
                new DotCoreDateComponentEntity($this->getFieldDate(), DotCoreDateComponentEntity::DATE_COMPONENT_MONTH),
                $month
            )
        );

        return $this
            ->Restraints($restraint);
    }

    /**
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return DotCoreEventBLL
     */
    public function ByDate($year, $month, $day) {
        $restraint = new DotCoreDALRestraint();
        $restraint
            ->AddRestraint(
                new DotCoreEntityRestraint(
                    new DotCoreDateComponentEntity($this->getFieldDate(), DotCoreDateComponentEntity::DATE_COMPONENT_YEAR),
                    $year
                )
            )
            ->AddRestraint(
                new DotCoreEntityRestraint(
                    new DotCoreDateComponentEntity($this->getFieldDate(), DotCoreDateComponentEntity::DATE_COMPONENT_MONTH),
                    $month
                )
            )
            ->AddRestraint(
                new DotCoreEntityRestraint(
                    new DotCoreDateComponentEntity($this->getFieldDate(), DotCoreDateComponentEntity::DATE_COMPONENT_DAY),
                    $day
                )
            );

        return $this
            ->Restraints($restraint);
    }

    /**
     *
     * @return DotCoreEventBLL
     */
    public function OrderedByDate() {
        $order = new DotCoreDALSelectionOrder();
        $order->AddOrderUnit(
            new DotCoreFieldSelectionOrder($this->getFieldDate(), DotCoreFieldSelectionOrder::DIRECTION_DESC)
        );
        return $this->Order($order);
    }

    public function HasEventsAfter($year, $month) {
        $restraints = new DotCoreDALRestraint();
        $restraints->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldDate(), $year.'-'.$month.'-31', DotCoreFieldRestraint::OPERATION_GREATER_THAN)
        );
        return $this->Restraints($restraints)->GetCount() > 0;
    }

    public function HasEventsBefore($year, $month) {
        $restraints = new DotCoreDALRestraint();
        $restraints->AddRestraint(
            new DotCoreFieldRestraint($this->getFieldDate(), $year.'-'.$month.'-01', DotCoreFieldRestraint::OPERATION_LESS_THAN)
        );
        return $this->Restraints($restraints)->GetCount() > 0;
    }

}
?>
