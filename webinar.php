<?php
require_once('crest/src/crest.php');

$B24_FIELDS = [
    'GC_ID'             => 'UF_CRM_1647603615324',  // ID регистрации в GetCourse
    'visited_webinar'   => 'UF_CRM_1656935799',     // Посетил вебинар
];

$sUserId = htmlspecialchars($_GET['user_id']);
$sWebinarId = htmlspecialchars($_GET['webinar_id']);
$sWebinarName = htmlspecialchars($_GET['webinar_name']);
$leadId = null;
$leadVisitedWebinar = '';
$today = date('d-m-Y');

$resultLeadList = CRest::call(
    'crm.lead.list',
    [
        'filter' => [
            $B24_FIELDS['GC_ID'] => $sUserId
        ],
        'select' => [
            'ID',
            $B24_FIELDS['GC_ID'],
            $B24_FIELDS['visited_webinar']
        ]
    ]
);

if (!empty($resultLeadList['result'])) {
    $lead = $resultLeadList['result'][0];
    $leadId = $lead['ID'];
    $leadVisitedWebinar = $lead[$B24_FIELDS['visited_webinar']];
}

if ($leadId) {
    $resultLeadUpdate = CRest::call(
        'crm.lead.update',
        [
            'id' => $leadId,
            'fields' => [
                $B24_FIELDS['visited_webinar'] => "{$sWebinarName} ({$today}) id: {$sWebinarId}\n{$leadVisitedWebinar}"
            ],
            'params' => [ 'REGISTER_SONET_EVENT' => 'Y' ]
        ]
    );
    if (!empty($resultLeadUpdate['result'])) {
    echo json_encode(['message' => 'Lead updated']);
    } elseif (!empty($resultLeadUpdate['error_description'])) {
        echo json_encode(['message' => 'Lead not updated: ' . $resultLeadUpdate['error_description']]);
    } else {
        echo json_encode(['message' => 'Lead not updated']);
    }
}

echo json_encode($resultLeadList);
