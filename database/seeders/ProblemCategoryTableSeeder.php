<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProblemCategory;

class ProblemCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin_issues = [
            'Administrative Case/Complaint',
            'Overtime Request-Related Concern',
            'Policy on Overtime Services Inquiries',
            'Annual SALN Submission/Inquiries',
            'Grievance Concern',
            'Extension of Service',
            'Sexual Harassment Complaint',
            'Reassignment',
            'Recall',
            'Pre-Termination',
            'Data Privacy',
            'HR Coordinator Replacement',
        ];
        $payroll_issues = [
            'Salary Deduction (LWOP/UL)',
            'Salary Adjustment/Differential',
            'No Salary Received',
            'No Benefits/Bonuses Received',
            'Overtime Pay/Allowance',
            'Hazard Pay, Subsistence and Laundry Allowance',
            'Request for Certificate of Last Salary Received',
            'Request for Certificate of No Bonus/Benefits Received',
            'Salary Underpayment/Overpayment',
            'Status of First Salary',
            'Terminal Leave Clearance',
            'Status of Special/Other Payroll',
            'Status of Last Salary',
        ];
        $records_issues = [
            'DTR UL Rectification',
            'Request of Certificate of LWOP',
            'Request of Certificate of No LWOP',
            'Request of Certificate of Leave Credits',
            'Request of Document from 201 File',
            'Salary Deductions Concerns',
            'Leave Application for Certain Types of Leave',
            'Tagging of Employees',
            'Schedule for i-Face/Biometrics Registration',
            'Incident Report On i-Face/Biometric Malfunction',
            'Transfer of Leave Credits from Other Agency to LGU Pasig or Vice Versa',
            'Terminal Leave Benefits',
        ];
        $claims_issues = [
            'Approval of Loan',
            'Loan Stoppage',
            'Loan Deduction',
            'Loan Availment',
            'GSIS Loan',
            'Policy Loan',
            'PAG-IBIG Loan',
            'PAG-IBIG MP2',
            'PAG-IBIG Contribution Upgrade',
            'Landbank Loan',
            'EPP Loan',
            'GSIS Reconciliation',
            'Membership Requirements',
            'GSIS BP Number',
            'PAG-IBIG Number',
            'PHILHEALTH Number',
            'GSIS Claim',
            'PHILHEALTH Claim',
            'GSIS Touch',
            'Rewards and Recognition',
            'APE Concerns',
            'Financial Literacy Seminar',
            'PASIGLAKAS',
            'CSS Fun Run',
            'Employee Fun Run',
            'Lost access in the loyalty award/incentive module',
            'Summary list of Pag-IBIG MP2 number',
            'Loan deduction',
            'Loans in the loan monitoring index is not reflected in the payroll process',
            'Loan schedule with number of payments and remaining balance',
        ];
        $rsp_issues = [
            'Request for Employment Records (eg. COE, CEC, SR, NPC)',
            'Status of Request for Employment Records (eg. COE, CEC, SR, NPC)',
            'Status of Job application',
            'Status of Recruitment Request Form',
            'Status of Renewal/Non-Renewal of Employees',
            'Status of Update on Personal Information',
            'Status of Update on Separated employees (Retired, Resigned, Dropped, terminated, for transfer, etc)',
            'Access of status from Admin Div for Admin cases (list; view only)',
            'Access of status from Admin Div for memos of Dropped, termination, Suspension',
            'Access of status from Record Div  for receiving of Death Cert and resignation letter',
        ];
        $lnd_issues = [
            'ID request',
            'Official Business (OB form) training-related only',
            'Scholarship Concern',
            'Study Leave Concern',
            'Activity Design Concerns',
            'Training / Travel Order Concern',
            'Concern on Request for Participation to External Trainings',
        ];
        $pm_issues = [
            'Request for Certified True Copy of Performance Ratings',
            'OPCR/DPCR and IPCR Appeals',
            'Request for System Access Extension',
            'Log-In Credential Issues',
            'Request to Edit Submitted OPCR/DPCR IPCR Targets/Accomplishments / Ratings',
            'Request to Submit OPCR/DPCR /IPCR for Newly Hired Employees',
            'Request for Technical Assistance',
            'Technical Issues /Glitch During Submission',
            'Access of OPCR/DPCR/IPCR Issue',
        ];
        // $it_issues = ['IT ISSUE'];

        // foreach ($it_issues as $it_issue) {
        //     ProblemCategory::create([
        //         'department_id' => 1,
        //         'office_id' => 2,
        //         'category_name' => $it_issue,
        //     ]);
        // }
        foreach ($admin_issues as $admin_issue) {
            ProblemCategory::create([
                'department_id' => 1,
                'office_id' => 3,
                'category_name' => $admin_issue,
            ]);
        }
        foreach ($payroll_issues as $payroll_issue) {
            ProblemCategory::create([
                'department_id' => 1,
                'office_id' => 4,
                'category_name' => $payroll_issue,
            ]);
        }
        foreach ($records_issues as $records_issue) {
            ProblemCategory::create([
                'department_id' => 1,
                'office_id' => 5,
                'category_name' => $records_issue,
            ]);
        }
        foreach ($claims_issues as $claims_issue) {
            ProblemCategory::create([
                'department_id' => 1,
                'office_id' => 6,
                'category_name' => $claims_issue,
            ]);
        }
        foreach ($rsp_issues as $rsp_issue) {
            ProblemCategory::create([
                'department_id' => 1,
                'office_id' => 7,
                'category_name' => $rsp_issue,
            ]);
        }
        foreach ($lnd_issues as $lnd_issue) {
            ProblemCategory::create([
                'department_id' => 1,
                'office_id' => 8,
                'category_name' => $lnd_issue,
            ]);
        }
        foreach ($pm_issues as $pm_issue) {
            ProblemCategory::create([
                'department_id' => 1,
                'office_id' => 9,
                'category_name' => $pm_issue,
            ]);
        }
    }
}
