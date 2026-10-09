<?php 
include "config.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PhilHealth Member Registration</title>
    <link rel="stylesheet" href="css/index.css">
    <link href='https://fonts.googleapis.com/css?family=Alan Sans' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oswald|Noto+Sans">
</head>
<body>
<div class="top-navbar">
    <img src="pic1.png" alt="PhilHealth Logo" class="header-logo">
    <div class="nav-controls">
        <form action="query.php" method="GET" class="search-portal-box">
            <input type="text" name="term"placeholder="Search PIN Number..." required>
            <button type="submit"> Search </button>
        </form>
        <a href="dashboard.php" class="btn-admin-gate"> Login </a>
    </div>
</div>

<div class="container">
    <h1>PhilHealth Member Registration Form</h1>
    
    <div class="step-indicator">
        <div class="step active" id="step0"><div class="circle">1</div>Personal Details</div>
        <div class="step" id="step1"><div class="circle">2</div>Address & Contact</div>
        <div class="step" id="step2"><div class="circle">3</div>Dependents</div>
        <div class="step" id="step3"><div class="circle">4</div>Member Type</div>
    </div>

    <form action="save-member.php" method="POST" id="pmrfForm">
        
        <div class="form-page active-page" id="page0">
            <div class="section-title">PERSONAL DETAILS</div>
            
            <div class="form-group full-width">
                <label>Member's Full Name</label>
                <div class="name-row">
                    <input type="text" name="member_last_name" id="member_last_name" placeholder="Last Name" required>
                    <input type="text" name="member_first_name" id="member_first_name" placeholder="First Name" required>
                    <input type="text" name="member_middle_name" id="member_middle_name" placeholder="Middle Name">
                    <input type="text" name="member_name_extension" id="member_name_extension" placeholder="Extension (opt)">
                </div>
                <div class="checkbox-group" style="margin-top: 5px;">
                    <label><input type="checkbox" name="member_no_middle_name" id="mem_no_mid" onchange="document.getElementById('member_middle_name').disabled = this.checked;"> No middle name</label>
                    <label><input type="checkbox" name="member_mononym" onchange="let m = this.checked; document.getElementById('member_first_name').disabled = m; document.getElementById('member_middle_name').disabled = m; document.getElementById('mem_no_mid').disabled = m;"> Mononym name</label>
                </div>
            </div>
            
            <div class="form-group full-width">
                <label>Mother's Maiden Name</label>
                <div class="name-row">
                    <input type="text" name="mother_last_name" id="mother_last_name" placeholder="Last Name" required>
                    <input type="text" name="mother_first_name" id="mother_first_name" placeholder="First Name" required>
                    <input type="text" name="mother_middle_name" id="mother_middle_name" placeholder="Middle Name">
                    <input type="text" name="mother_name_extension" id="mother_name_extension" placeholder="Extension (opt)">
                </div>
                <div class="checkbox-group" style="margin-top: 5px;">
                    <label><input type="checkbox" name="mother_no_middle_name" id="mot_no_mid" onchange="document.getElementById('mother_middle_name').disabled = this.checked;"> No middle name</label>
                    <label><input type="checkbox" name="mother_mononym" onchange="let m = this.checked; document.getElementById('mother_first_name').disabled = m; document.getElementById('mother_middle_name').disabled = m; document.getElementById('mot_no_mid').disabled = m;"> Mononym name</label>
                </div>
            </div>
            
            <div class="form-group full-width">
                <label>Spouse (If married) - Optional</label>
                <div class="name-row">
                    <input type="text" name="spouse_last_name" id="spouse_last_name" placeholder="Last Name">
                    <input type="text" name="spouse_first_name" id="spouse_first_name" placeholder="First Name">
                    <input type="text" name="spouse_middle_name" id="spouse_middle_name" placeholder="Middle Name">
                    <input type="text" name="spouse_name_extension" id="spouse_name_extension" placeholder="Extension (opt)">
                </div>
                <div class="checkbox-group" style="margin-top: 5px;">
                    <label><input type="checkbox" name="spouse_no_middle_name" id="spo_no_mid" onchange="document.getElementById('spouse_middle_name').disabled = this.checked;"> No middle name</label>
                    <label><input type="checkbox" name="spouse_mononym" onchange="let m = this.checked; document.getElementById('spouse_first_name').disabled = m; document.getElementById('spouse_middle_name').disabled = m; document.getElementById('spo_no_mid').disabled = m;"> Mononym name</label>
                </div>
            </div>
            
            <div class="row-2cols">
                <div class="form-group">
                    <label>Sex</label>
                    <select name="sex" required>
                        <option value="">Select</option>
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Civil Status</label>
                    <select name="civil_status" id="civil_status" required>
                        <option value="">Select</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Widower">Widower</option>
                        <option value="Legally Separated">Legally Separated</option>
                        <option value="Annulled">Annulled</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" required>
            </div>
            <div class="form-group">
                <label>Place of Birth</label>
                <input type="text" name="place_of_birth" required>
            </div>
            
            <div class="form-group">
                <label>Citizenship</label>
                <select name="member_citizenship" required>
                    <option value="">Select</option>
                    <option value="Filipino">Filipino</option>
                    <option value="Dual Citizenship">Dual Citizenship</option>
                    <option value="Foreign National">Foreign National</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>PhilSys ID (Optional)</label>
                <input type="text" name="philsys_id" id="philsys_id" placeholder="0000-0000-0000-0000" maxlength="19">
            </div>
            <div class="form-group">
                <label>TIN ID (Optional)</label>
                <input type="text" name="tin_id" id="tin_id" placeholder="000-000-000" maxlength="11">
            </div>
            
            <div class="form-group full-width">
                <label>Preferred Konsulta Provider (Leave it as blank if none)</label>
                <input type="text" name="konsulta_provider" placeholder="e.g. Quezon City General Hospital">
            </div>
        </div>

        <div class="form-page" id="page1">
            <div class="section-title">ADDRESS and CONTACT DETAILS</div>
            
            <div class="form-group full-width">
                <label>Permanent Home Address</label>
                <input type="text" name="permanent_home_add" id="permanent_home_add" required>
                <div class="address-hint">Unit/Room No. | Building | Lot/Block | Street | Barangay | City | Province | ZIP</div>
            </div>
            
            <div class="form-group full-width">
                <label>Mailing Address</label>
                <input type="text" name="mailing_add" id="mailing_add" required>
                <div class="checkbox-group" style="margin-top: 5px;">
                    <label><input type="checkbox" id="same_as_permanent"> Same as Permanent Home Address</label>
                </div>
            </div>
            
            <div class="form-group">
                <label>Mobile Number (Primary)</label>
                <input type="text" name="mobile_num" placeholder="e.g. 09123456789" required>
            </div>
            <div class="form-group">
                <label>Business Direct Line (Optional)</label>
                <input type="text" name="business_d_line" id="business_line" placeholder="0000-0000" maxlength="9">
            </div>
            <div class="form-group">
                <label>Home Phone Number (Optional)</label>
                <input type="text" name="home_phone_num" id="home_phone" placeholder="00-0000-0000" maxlength="12">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email_add">
            </div>
        </div>

        <div class="form-page" id="page2">
            <div class="section-title">DECLARATION OF DEPENDENTS</div>
            <button type="button" class="btn-add-dep" onclick="addDependent()">+ Add Dependent</button>
            <div id="dependentsContainer"></div>
        </div>

        <div class="form-page" id="page3">
            <div class="section-title">MEMBER TYPE</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Member Type</label>
                    <select name="member_type" id="member_type" required>
                        <option value="">-- Select Member Type --</option>
                        <option value="Direct Contributor">Direct Contributor</option>
                        <option value="Indirect Contributor">Indirect Contributor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Contributor Type</label>
                    <select name="contributor_type" id="contributor_type" required>
                        <option value="">-- Select Member Type First --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Profession / Occupation Statement</label>
                    <input type="text" name="profession" id="profession">
                </div>
                <div class="form-group">
                    <label>Monthly Declarable Income (PHP)</label>
                    <input type="number" step="0.01" name="monthly_income" id="monthly_income" placeholder="0.00">
                </div>
                <div class="form-group full-width">
                    <label>Provided Proof of Income Document</label>
                    <input type="text" name="proof_of_income">
                </div>
            </div>
        </div>

        <div class="nav-buttons">
            <button type="button" class="btn-prev" id="prevBtn" onclick="prevPage()" style="display:none;">Back</button>
            <button type="button" class="btn-next" id="nextBtn" onclick="nextPage()">Next Step</button>
            <button type="submit" class="btn-submit" id="submitBtn" style="display:none;">Submit Form</button>
        </div>

    </form>
</div>

<script>
    // Dynamic Contributor Type Logic based on Image Rules
    const contributorOptions = {
        "Direct Contributor": [
            "Employed Private", "Employed Government", "Professional Practitioner",
            "Self-Earning Individual", "Kasambahay", "Migrant Worker",
            "Lifetime Member", "Filipinos with Dual Citizenship / Living Abroad",
            "Foreign National", "Family Driver"
        ],
        "Indirect Contributor": [
            "Listahanan", "4Ps/MCCT", "Senior Citizen", "PAMANA", "KIA/KIPO",
            "LGU-sponsored", "NGA-sponsored", "Private-sponsored",
            "Person with Disability", "Bangsamoro/Normalization"
        ]
    };

    document.getElementById('member_type').addEventListener('change', function() {
        const type = this.value;
        const subSelect = document.getElementById('contributor_type');
        
        // Clear current options
        subSelect.innerHTML = '<option value="">-- Select Contributor Type --</option>';
        
        if (contributorOptions[type]) {
            contributorOptions[type].forEach(option => {
                const opt = document.createElement('option');
                opt.value = option;
                opt.textContent = option;
                subSelect.appendChild(opt);
            });
        }
    });

    let currentPage = 0;
    const totalPages = 4;
    let dependentCount = 0;
    
    function showPage(pageNum) {
        for(let i = 0; i < totalPages; i++) {
            document.getElementById(`page${i}`).classList.remove('active-page');
        }
        document.getElementById(`page${pageNum}`).classList.add('active-page');
        
        for (let i = 0; i < totalPages; i++) {
            const step = document.getElementById(`step${i}`);
            step.classList.remove('active', 'completed');
            if (i < pageNum) step.classList.add('completed');
            else if (i === pageNum) step.classList.add('active');
        }
        
        document.getElementById('prevBtn').style.display = pageNum === 0 ? 'none' : 'inline-block';
        
        if (pageNum === (totalPages - 1)) {
            document.getElementById('nextBtn').style.display = 'none';
            document.getElementById('submitBtn').style.display = 'inline-block';
        } else {
            document.getElementById('nextBtn').style.display = 'inline-block';
            document.getElementById('submitBtn').style.display = 'none';
        }
    }
    
    function nextPage() {
        const currentPageElement = document.getElementById(`page${currentPage}`);
        const required = currentPageElement.querySelectorAll('[required]');
        
        for (let field of required) {
            if (field.disabled) continue;
            if (!field.value || field.value.trim() === '') {
                field.style.border = '2px solid red';
                field.focus();
                alert('Please fill out all required fields.');
                setTimeout(() => field.style.border = '', 2000);
                return;
            }
        }
        
        if (currentPage < (totalPages - 1)) {
            currentPage++;
            showPage(currentPage);
        }
    }
    
    function prevPage() {
        if (currentPage > 0) {
            currentPage--;
            showPage(currentPage);
        }
    }
    
    document.getElementById('same_as_permanent').addEventListener('change', function() {
        const mailingField = document.getElementById('mailing_add');
        if (this.checked) {
            mailingField.value = 'Same as Above';
            mailingField.disabled = true;
            mailingField.classList.add('disabled-field');
        } else {
            mailingField.value = '';
            mailingField.disabled = false;
            mailingField.classList.remove('disabled-field');
        }
    });

    // Dynamic Validation: Require Spouse Name ONLY if Married
    document.getElementById('civil_status').addEventListener('change', function() {
        const status = this.value;
        const spouseLast = document.getElementById('spouse_last_name');
        const spouseFirst = document.getElementById('spouse_first_name');
        
        if (status === 'Married') {
            spouseLast.setAttribute('required', 'true');
            spouseFirst.setAttribute('required', 'true');
            spouseLast.placeholder = 'Last Name (Required)';
            spouseFirst.placeholder = 'First Name (Required)';
        } else {
            spouseLast.removeAttribute('required');
            spouseFirst.removeAttribute('required');
            spouseLast.placeholder = 'Last Name';
            spouseFirst.placeholder = 'First Name';
            spouseLast.value = '';
            spouseFirst.value = '';
            document.getElementById('spouse_middle_name').value = '';
            document.getElementById('spouse_name_extension').value = '';
            spouseLast.style.border = '';
            spouseFirst.style.border = '';
        }
    });
    
    document.getElementById('philsys_id')?.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 16) value = value.slice(0, 16);
        let formatted = '';
        for (let i = 0; i < value.length; i++) {
            if (i === 4 || i === 8 || i === 12) formatted += '-';
            formatted += value[i];
        }
        this.value = formatted;
    });
    
    document.getElementById('tin_id')?.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 9) value = value.slice(0, 9);
        let formatted = '';
        for (let i = 0; i < value.length; i++) {
            if (i === 3 || i === 6) formatted += '-';
            formatted += value[i];
        }
        this.value = formatted;
    });
    
    document.getElementById('business_line')?.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 8) value = value.slice(0, 8);
        let formatted = '';
        for (let i = 0; i < value.length; i++) {
            if (i === 4) formatted += '-';
            formatted += value[i];
        }
        this.value = formatted;
    });
    
    document.getElementById('home_phone')?.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        let formatted = '';
        for (let i = 0; i < value.length; i++) {
            if (i === 2 || i === 6) formatted += '-';
            formatted += value[i];
        }
        this.value = formatted;
    });
    

    function addDependent() {
        const container = document.getElementById('dependentsContainer');
        const id = dependentCount;
        const div = document.createElement('div');
        div.className = 'dependent-card';
        div.id = `dep_${id}`;
        
        const fnId = `dep_first_${id}`;
        const mnId = `dep_mid_${id}`;
        
        div.innerHTML = `
            <div class="dependent-header">
                <h4>Dependent #${dependentCount + 1}</h4>
                <button type="button" class="btn-remove-dep" onclick="removeDependent(${id})">Remove</button>
            </div>
            <div class="dependent-name-group">
                <input type="text" name="dep_last_name[]" placeholder="Last Name" required>
                <input type="text" name="dep_first_name[]" id="${fnId}" placeholder="First Name" required>
                <input type="text" name="dep_middle_name[]" id="${mnId}" placeholder="Middle Name">
                <input type="text" name="dep_extension[]" placeholder="Extension">
            </div>
            <div class="checkbox-group" style="margin-top: 10px; margin-bottom: 15px;">
                <input type="hidden" name="dep_no_middle_name[]" value="off">
                <label>
                    <input type="checkbox" onchange="this.previousElementSibling.value = this.checked ? 'on' : 'off'; document.getElementById('${mnId}').disabled = this.checked;"> 
                    No middle name
                </label>
                
                <input type="hidden" name="dep_mononym[]" value="off">
                <label>
                    <input type="checkbox" onchange="this.previousElementSibling.value = this.checked ? 'on' : 'off'; let m = this.checked; document.getElementById('${fnId}').disabled = m; document.getElementById('${mnId}').disabled = m;"> 
                    Mononym name
                </label>
            </div>
            <div class="dependent-details-group">
                <select name="dep_relationship[]" required>
                    <option value="Spouse">Spouse</option><option value="Child">Child</option><option value="Parent">Parent</option>
                </select>
                <input type="date" name="dep_birth[]" required>
                <select name="dep_citizenship[]">
                    <option value="Filipino">Filipino</option><option value="Dual Citizenship">Dual Citizenship</option><option value="Foreign National">Foreign National</option>
                </select>
                <select name="dep_disability[]">
                    <option value="NO">No Disability</option><option value="YES">Permanent Disability</option>
                </select>
            </div>
        `;
        container.appendChild(div);
        dependentCount++;
    }
    
    function removeDependent(id) {
        const el = document.getElementById(`dep_${id}`);
        if (el) el.remove();
    }
    
    showPage(0);
</script>
</body>
</html>