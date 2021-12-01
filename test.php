<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/ajax/tools.php");
?>

<?
function im($user_id,$msg){	
	$msgbox = REST_API('im.notify', array('to' =>$user_id,'message'=>$msg), $domain, $accessToken);
}

$entity_id = $_REQUEST['data']['FIELDS']['ID'];
if($entity_id!=null){
$_REQUEST['id']=$entity_id;
}

if($_REQUEST['id']==null){
$resultotal =REST_API('crm.deal.list', array('FILTER'=> array('>ORIGIN_ID'=> 0)));
$calc =  $resultotal['total']-50;

for($i=0;$i<$resultotal['total'];$i=($i+50)){ 
			$crm_deal_qvery[] = 'crm.deal.list?'.http_build_query(array('FILTER'=> array('>ORIGIN_ID'=> 0),'start'=>$i));
		}	
$deal_list_batch = REST_API('batch', array('cmd' => $crm_deal_qvery));
$last_arr_deal = array_pop($deal_list_batch['result']['result']);
$last_deal =  array_pop($last_arr_deal) ;
$_REQUEST['id']=$last_deal['ID'];
}

$result =REST_API('crm.deal.get', array("ID"=>$_REQUEST['id']));
$result_arr=$result['result'];
$site_id=$result['result']['ORIGIN_ID'];

if(is_numeric($site_id)){
CModule::IncludeModule('iblock');
CModule::IncludeModule('sale');

$arOrderProps = [];
$dbRes = \Bitrix\Sale\PropertyValue::getList([
    'filter' => ['=ORDER_ID' => $site_id]
]);
while($arRes = $dbRes->fetch()) {
    $arOrderProps[$arRes['CODE']] = $arRes['VALUE'];
}

$filter = Array("EMAIL" => $arOrderProps['EMAIL']);
$sql = CUser::GetList(($by="id"), ($order="desc"), $filter);
if($sql->NavNext(true, "f_"))
{
	$id_user = $f_ID;
}
$rsUsers = CUser::GetByID($id_user);
while ($arUser = $rsUsers->Fetch()) {
      $userEmail = $arUser;
}


$dbRes = \Bitrix\Sale\Order::getList([
    'filter' => [
	'=ID'=>$site_id
    ],
    'order' => ['ID' => 'DESC']
]);
     
while ($order = $dbRes->fetch())
{
	$arOrderPropsOrder = $order;
}

$db_ptype = CSalePaySystem::GetList($arOrder = Array("SORT"=>"ASC", "PSA_NAME"=>"ASC"), Array("LID"=>$arOrderPropsOrder['LID'], "CURRENCY"=>"RUB", "ACTIVE"=>"Y", "PERSON_TYPE_ID"=>$arOrderPropsOrder['PERSON_TYPE_ID'],'ID'=>$arOrderPropsOrder['PAY_SYSTEM_ID']));
$bFirst = True;
while ($ptype = $db_ptype->Fetch())
{
	$pay=$ptype;

}


if(trim($userEmail['UF_INN']) == NULL){
$partner=248;
$category_id='10';
$stage_id='C10:NEW';
}else{
$partner=246;
$category_id=12;
$stage_id='C12:NEW';
}


$params_add= array(
'ID'=>$_REQUEST['id'],
'FIELDS' => array(
		'STAGE_ID'=> $stage_id,
		'CATEGORY_ID'=> $category_id, // общее направление
		'UF_CRM_1584460112836'=>$partner,
		'UF_CRM_1584459539830'=>$arOrderProps['PHONE'],//phone
		'UF_CRM_1584459530383'=>$arOrderProps['EMAIL'],//email
		'UF_CRM_1633425702'=>$arOrderProps['ADDRESS'],//adress
		'UF_CRM_1584459905775'=>$arOrderProps['CITY'],//city
		'UF_CRM_1633430470'=>$userEmail['UF_INN'],//city
		'UF_CRM_1636468508268'=>$pay['NAME'],//pay
		'TITLE'=>$result_arr['TITLE'],
		'TYPE_ID'=>$result_arr['TYPE_ID'],
		'PROBABILITY'=>$result_arr['PROBABILITY'],
		'PROBABILITY'=>$result_arr['PROBABILITY'],
		'OPPORTUNITY'=>$result_arr['OPPORTUNITY'],
		'IS_MANUAL_OPPORTUNITY'=>$result_arr['IS_MANUAL_OPPORTUNITY'],
		'TAX_VALUE'=>$result_arr['TAX_VALUE'],
		'LEAD_ID'=>$result_arr['LEAD_ID'],
		'COMPANY_ID'=>$result_arr['COMPANY_ID'],
		'CONTACT_ID'=>$result_arr['CONTACT_ID'],
		'QUOTE_ID'=>$result_arr['QUOTE_ID'],
		'BEGINDATE'=>$result_arr['BEGINDATE'],
		'CLOSEDATE'=>$result_arr['CLOSEDATE'],
		'ASSIGNED_BY_ID'=>$result_arr['ASSIGNED_BY_ID'],
		'CREATED_BY_ID'=>$result_arr['CREATED_BY_ID'],
		'MODIFY_BY_ID'=>$result_arr['MODIFY_BY_ID'],
		'DATE_CREATE'=>$result_arr['DATE_CREATE'],
		'DATE_MODIFY'=>$result_arr['DATE_MODIFY'],
		'OPENED'=>$result_arr['OPENED'],
		'CLOSED'=>$result_arr['CLOSED'],
		'COMMENTS'=>$result_arr['COMMENTS'],
		'ADDITIONAL_INFO'=>$result_arr['ADDITIONAL_INFO'],
		'LOCATION_ID'=>$result_arr['LOCATION_ID'],
		'IS_NEW'=>$result_arr['IS_NEW'],
		'IS_RETURN_CUSTOMER'=>$result_arr['IS_RETURN_CUSTOMER'],
		'IS_REPEATED_APPROACH'=>$result_arr['IS_REPEATED_APPROACH'],
		'ORIGINATOR_ID'=>$result_arr['ORIGINATOR_ID'],
		'ORIGIN_ID'=>$result_arr['ORIGIN_ID'],
		'UF_CRM_1633424643'=>$result_arr['UF_CRM_1633424643'],
		'UF_CRM_1638360933'=> $category_id,
));

$productrows =REST_API('crm.deal.productrows.get', array('ID'=>$_REQUEST['id']));

echo '<pre>result';echo print_r($result,true);echo '</pre>';
echo '<pre>rows_add';echo print_r($rows_add,true);echo '</pre>';
echo '<pre>params_add';echo print_r($params_add,true);echo '</pre>';
echo '<pre>productrows';echo print_r($productrows,true);echo '</pre>';

	if($result_arr['UF_CRM_1638360933']==null){
		$delete =REST_API('crm.deal.delete', array('ID'=>$_REQUEST['id']));
		$add =REST_API('crm.deal.add', $params_add);
		$rows_add=array('ID'=>$add['result'],
		'rows'=>$productrows['result']);
		$productrows_set =REST_API('crm.deal.productrows.set', $rows_add);
	}

}


?>