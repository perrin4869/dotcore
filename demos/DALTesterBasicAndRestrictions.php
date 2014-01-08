<?php
echo '<strong>News DAL Test:</strong><br /><br />';

$newsBLL = new DotCoreNewsBLL();

$restraint = new DotCoreDALRestraint();
$restraint
    ->AddRestraint(
        new DotCoreFieldRestraint($newsBLL->getFieldShortContent(), '%של%', DotCoreFieldRestraint::OPERATION_LIKE))
    ->ChangeRestraintAddingMethod(DotCoreDALRestraint::RESTRAINT_ADDING_METHOD_OR)
    ->OpenRestrainingUnit()
    ->AddRestraint(
        new DotCoreFieldRestraint($newsBLL->getFieldNewsID(), 3, DotCoreFieldRestraint::OPERATION_GREATER_OR_EQUAL))
    ->ChangeRestraintAddingMethod(DotCoreDALRestraint::RESTRAINT_ADDING_METHOD_AND)
    ->AddRestraint(
        new DotCoreFieldRestraint($newsBLL->getFieldNewsID(), 11, DotCoreFieldRestraint::OPERATION_LESS_OR_EQUAL))
    ->CloseRestrainingUnit();

$order = new DotCoreDALSelectionOrder();
$order
    ->AddOrderUnit(
        new DotCoreFieldSelectionOrder(
            $newsBLL->getFieldNewsID(),
            DotCoreFieldSelectionOrder::DIRECTION_DESC));

$records = $newsBLL
    ->Restraints($restraint)
    ->Order($order)
    ->Offset(2)
    ->Limit(2)
    ->Select();

foreach($records as $record)
{
    echo 'ID: ' . $record->getID();
    echo '<br />';
    echo 'Short: ' . $record->getNewsShortContent();
    echo '<br /><br />';
}

echo '<strong>Insertion/Update test:</strong><br />';

/* @var $record DotCoreNewsRecord */
$record = $records[0];
// $record->setNewsShortContent('הכנס יציג תוכניות שונות של ממשלת ארצות הברית (EXIM) למימון פרויקטים בישראל ובעולם בעת רכישת ציוד מתוצרת ארה"ב. הכנס מיועד למנכ"לים, מנהלי כספים ויזמים בחברות העוסקות בפרוייקטים של הנדסה, מים חקלאות, רפואה, אקולוגיה ואנרגיה מתחדשת');
$newsBLL->Save($record);

$record = $newsBLL->GetNewRecord();
$record->setNewsLanguageID(1);
$record->setNewsTitle('Title 1');
$record->setNewsShortContent('Short');
// $newsDAL->Save($record);

// Eilat Trading Event
$eventsBLL = new ChamberEilatEventBLL();
$restraint = new DotCoreDALRestraint();
$restraint
    ->AddRestraint(
        new DotCoreFulltextRestraint($eventsBLL->getSearchFulltext(), 'ארצות הברית'));

$results = $eventsBLL->Restraints($restraint)->Select();


echo '<br /><br /><br />';
echo '<strong>Events Searching Test:</strong> <br />';

echo count($results) . ' Results:<br />';
foreach($results as $result)
{
    echo 'ID: ' . $result->getEventID();
    echo '<br />';
    echo 'Details: ' . $result->getEventDetails();
    echo '<br /><br />';
}

echo '<br /><br />';
?>
