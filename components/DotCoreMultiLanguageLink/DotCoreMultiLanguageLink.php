<?php

/**
 * DotCoreMultiLanguageLink - Describes a link between one DAL, its multilanguage properties in another DAL
 *
 * @author perrin
 */
class DotCoreMultiLanguageLink extends DotCoreDALLink {

    public function  __construct(
        DotCoreOneToManyRelationship $relationship,
        DotCoreDALField $language_field,
        $language_id = NULL,
        $link_type = DotCoreDALLink::LINK_TYPE_LEFT,
        $additional_restraints = NULL,
        $store_link_results = TRUE)
    {
        $this->language_field = $language_field;
        if($additional_restraints == NULL) {
            if(!is_numeric($language_id)) {
                $language_id = self::$context_language_id;
            }
            if(is_numeric($language_id)) {
                $lang_field_path = $path == NULL ? new DotCoreDALPath() : clone($path);
                $lang_field_path->append($relationship->GetRelationshipName());
                $additional_restraints = new DotCoreDALRestraint();
                $additional_restraints
                    ->AddRestraint
                    (
                        new DotCoreFieldRestraint
                        (
                            new DotCoreDALFieldPath
                            (
                                $this->language_field,
                                $lang_field_path
                            ),
                            $language_id,
                            DotCoreFieldRestraint::OPERATION_EQUALS
                        )
                    );
            }
            
        }

        parent::__construct(
                $relationship,
                $relationship->GetPrimaryDAL(),
                $link_type, 
                $additional_restraints,
                $store_link_results);
        
    }

    /*
     *
     * Properties:
     *
     */

    /**
     * Holds the ID of the language we want the multilanguage records to contain
     * @var int
     */
    private static $context_language_id;

    /**
     *
     * @return int
     */
    public static function GetContextLanguageID() {
        return self::$context_language_id;
    }

    /**
     * Sets the language by which multilanguage components are returned
     * @param int $id
     */
    public static function SetContextLanguageID($id) {
        self::$context_language_id = $id;
    }

    /**
     * Holds the field that represents the language of the record in the DAL
     * @var DotCoreDALField
     */
    private $language_field = NULL;
    
}

?>