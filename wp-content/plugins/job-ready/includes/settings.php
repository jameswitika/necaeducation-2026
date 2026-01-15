<?php
// JobReady API Settings
 define('JR_API_SERVER', 'https://workflows.jobreadyplus.com'); // STAGING
//define('JR_API_SERVER', 'https://necaskillscentre.jobreadyrto.com.au'); // LIVE

// Debug Mode
define('ASC_DEBUG_MODE', false);
define('ASC_HOLDING_BAY_DEBUG_MODE', false);
define('CPD_DEBUG_MODE', false);
define('IOT_DEBUG_MODE', false);
define('JR_DEBUG_MODE', false);
define('NASC_DEBUG_MODE', false);
define('PREAPPRENTICE_DEBUG_MODE', false);
define('PROJECT_MGMNT_DIPLOMA_DEBUG_MODE', false);
define('UEE30820_DEBUG_MODE', false);

// Form ID references
define('ASC_HOLDING_BAY_APPLICATION_FORM', 105); // Previously 90 (22.11.2023)
define('CPD_FORM_ID', 91); // Previous 98 (07.03.2023)
define('IOT_FORM_ID', 99);
define('IOT_PRODUCT_ID', 24025);
define('JOB_REGISTRATION_FORM', 18);
define('NASC_REGISTRATION_FORM', 118);
define('NASC_PRODUCT_ID', 28883); // NASC Registration PRODUCT ID
define('NASC_ENROLMENT_FORM', 119);
define('NON_APPRENTICE_APPLICATION_FORM', 117); // Previously 112 (07.04.2024)
define('PRE_APPRENTICE_APPLICATION_FORM', 117); // Previously 112 (07.04.2024)
define('PRE_APPRENTICE_PRODUCT_ID', 4119); // Electrical Pre-Apprenticeship Application PRODUCT ID
define('PROJECT_MANAGEMENT_APPLICATION_FORM', 73);
define('PROJECT_MANAGEMENT_DIPLOMA_APPLICATION_FORM', 96); // Previously 82 (14.02.2023)
define('SHORT_COURSE_APPLICATION_FORM_ACCREDITED', 115); // Previously 104 (28.02.2024)
define('SHORT_COURSE_APPLICATION_FORM_NON_ACCREDITED', 114); // Previously 43 (28.02.2024)
define('UEE30820_APPLICATION_FORM', 121); // Previously 113 (11.11.2023)

// Job Ready Application URL
define('JOB_READY_APPLICATION_URL', 'http://60.240.76.70/370/apply.php');