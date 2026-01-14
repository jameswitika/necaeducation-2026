<?php
/*
 * JobReadyGTO
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JobReadyGTO
{
	var $txtFirstName;
	var $txtMiddleName;
	var $txtSurname;
	var $txtPreferredName;
	var $txtAddress;
	var $txtSuburb;
	var $drpState;
	var $txtPostcode;
	var $txtMobilePhone;
	var $txtHomePhone;
	var $txtEmail;
	var $txtDateOfBirth;
	var $rdoResidencyStatusList;
	var $rdoAboriginalTorresStraitIslandDescent;
	var $rdoHeardAboutUs;
	var $txtHeardAboutUsOther;
	var $have_you_attended_our_open_day; 			// Previously: have-you-attended-our-open-day
	var $txtJobRef;
	var $txtSiteCode;
	var $va_drpHighestQualification;
	var $va_rdoPreviouslyEmployed;
	var $va_rdoPreviouslyCompleted;
	var $va_drpCurrentlyEnrolled;
	var $va_rdoPreApprenticeship;
	var $va_drpCurrentlyStudyingCompletionMonth;
	var $va_drpCurrentlyStudyingCompletionYear;
	var $va_rdoNECAAptitudeTest;
	var $va_txtNECAAptitudeTestScore;
	var $x370t_drpHighestQualification;
	var $x370t_rdoPreviouslyEmployed;
	var $x370t_rdoPreviouslyCompleted;
	var $x370t_rdoOtherQualifications;
	var $x370t_txtCertificateIIIName;
	var $x370t_drpCertificateIIIStartMonth;
	var $x370t_drpCertificateIIIStartYear;
	var $x370t_drpCertificateIIIEndMonth;
	var $x370t_drpCertificateIIIEndYear;
	var $x370t_txtCertificateIVName;
	var $x370t_drpCertificateIVStartMonth;
	var $x370t_drpCertificateIVStartYear;
	var $x370t_drpCertificateIVEndMonth;
	var $x370t_drpCertificateIVEndYear;
	var $x370t_txtDiplomaDegree;
	var $x370t_drpDiplomaDegreeStartMonth;
	var $x370t_drpDiplomaDegreeStartYear;
	var $x370t_drpDiplomaDegreeEndMonth;
	var $x370t_drpDiplomaDegreeEndYear;
	var $fileCoverLetter;
	var $fileResume;
	var $cbPositionDescription;
	var $cbInjuryOrDisease;
	var $cbDiscloseToThirdParties;
	var $cbDeclarationAgreement;
	
	function __construct()
	{
		$this->txtFirstName = '';
		$this->txtMiddleName = '';
		$this->txtSurname = '';
		$this->txtPreferredName = '';
		$this->txtAddress = '';
		$this->txtSuburb = '';
		$this->drpState = '';
		$this->txtPostcode = '';
		$this->txtMobilePhone = '';
		$this->txtHomePhone = '';
		$this->txtEmail = '';
		$this->txtDateOfBirth = '';
		$this->rdoResidencyStatusList = '';
		$this->rdoAboriginalTorresStraitIslandDescent = '';
		$this->rdoHeardAboutUs = '';
		$this->txtHeardAboutUsOther = '';
		$this->have_you_attended_our_open_day = ''; 			// Previously: have-you-attended-our-open-day
		$this->txtJobRef = '';
		$this->txtSiteCode = '';
		$this->va_drpHighestQualification = '';
		$this->va_rdoPreviouslyEmployed = '';
		$this->va_rdoPreviouslyCompleted = '';
		$this->va_drpCurrentlyEnrolled = '';
		$this->va_rdoPreApprenticeship = '';
		$this->va_drpCurrentlyStudyingCompletionMonth = '';
		$this->va_drpCurrentlyStudyingCompletionYear = '';
		$this->va_rdoNECAAptitudeTest = '';
		$this->va_txtNECAAptitudeTestScore = '';
		$this->x370t_drpHighestQualification = '';
		$this->x370t_rdoPreviouslyEmployed = '';
		$this->x370t_rdoPreviouslyCompleted = '';
		$this->x370t_rdoOtherQualifications = '';
		$this->x370t_txtCertificateIIIName = '';
		$this->x370t_drpCertificateIIIStartMonth = '';
		$this->x370t_drpCertificateIIIStartYear = '';
		$this->x370t_drpCertificateIIIEndMonth = '';
		$this->x370t_drpCertificateIIIEndYear = '';
		$this->x370t_txtCertificateIVName = '';
		$this->x370t_drpCertificateIVStartMonth = '';
		$this->x370t_drpCertificateIVStartYear = '';
		$this->x370t_drpCertificateIVEndMonth = '';
		$this->x370t_drpCertificateIVEndYear = '';
		$this->x370t_txtDiplomaDegree = '';
		$this->x370t_drpDiplomaDegreeStartMonth = '';
		$this->x370t_drpDiplomaDegreeStartYear = '';
		$this->x370t_drpDiplomaDegreeEndMonth = '';
		$this->x370t_drpDiplomaDegreeEndYear = '';
		$this->fileCoverLetter = '';
		$this->fileResume = '';
		$this->cbPositionDescription = '';
		$this->cbInjuryOrDisease = '';
		$this->cbDiscloseToThirdParties = '';
		$this->cbDeclarationAgreement = '';
	}
}

class JobReadyGTOOperations
{
	function __construct()
	{
		
	}
}