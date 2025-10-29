// Counter variables for dynamic sections
let eduCount = 1, projCount = 1, workCount = 1, leadCount = 1;

// Add dynamic sections - these functions add new entry fields
function addEducation() {
  const section = document.getElementById('educationSection');
  const div = document.createElement('div');
  div.className = 'eduEntry';
  div.innerHTML = `
    <input type="text" name="education[${eduCount}][school]" placeholder="School / University">
    <input type="text" name="education[${eduCount}][degree]" placeholder="Degree">
    <input type="text" name="education[${eduCount}][dates]" placeholder="Dates">
  `;
  section.appendChild(div);
  eduCount++;
}

function addProject() {
  const section = document.getElementById('projectSection');
  const div = document.createElement('div');
  div.className = 'projEntry';
  div.innerHTML = `
    <input type="text" name="projects[${projCount}][title]" placeholder="Project Title">
    <input type="text" name="projects[${projCount}][dates]" placeholder="Dates">
    <textarea name="projects[${projCount}][bullets]" placeholder="Bullet points separated by |"></textarea>
  `;
  section.appendChild(div);
  projCount++;
}

function addWork() {
  const section = document.getElementById('workSection');
  const div = document.createElement('div');
  div.className = 'workEntry';
  div.innerHTML = `
    <input type="text" name="work[${workCount}][title]" placeholder="Job Title">
    <input type="text" name="work[${workCount}][dates]" placeholder="Dates">
    <input type="text" name="work[${workCount}][location]" placeholder="Location">
    <textarea name="work[${workCount}][bullets]" placeholder="Bullet points separated by |"></textarea>
  `;
  section.appendChild(div);
  workCount++;
}

function addLeadership() {
  const section = document.getElementById('leadershipSection');
  const div = document.createElement('div');
  div.className = 'leadEntry';
  div.innerHTML = `
    <input type="text" name="leadership[${leadCount}][title]" placeholder="Role / Position">
    <input type="text" name="leadership[${leadCount}][organization]" placeholder="Organization">
    <input type="text" name="leadership[${leadCount}][dates]" placeholder="Dates">
    <input type="text" name="leadership[${leadCount}][location]" placeholder="Location">
    <textarea name="leadership[${leadCount}][bullets]" placeholder="Bullet points separated by |"></textarea>
  `;
  section.appendChild(div);
  leadCount++;
}

// Make functions available globally for inline onclick handlers
window.addEducation = addEducation;
window.addProject = addProject;
window.addWork = addWork;
window.addLeadership = addLeadership;

// Update preview with form data
function updatePreview() {
  const form = document.getElementById('resumeForm');
  const preview = document.getElementById('preview');
  
  if (!form || !preview) {
    console.error('Form or preview element not found');
    return;
  }
  
  const formData = new FormData(form);

  let html = `<h1>${formData.get('name') || ''}</h1>`;
  html += `<p>${formData.get('phone') || ''} | ${formData.get('email') || ''} | ${formData.get('linkedin') || ''} | ${formData.get('website') || ''}</p>`;
  html += `<h2>Objective</h2><p>${formData.get('objective') || ''}</p>`;

  // Education section
  html += `<h2>Education</h2>`;
  for (let i = 0; ; i++) {
    if (!formData.get(`education[${i}][school]`)) break;
    html += `<p>${formData.get(`education[${i}][school]`)}<br>${formData.get(`education[${i}][degree]`)}<br>${formData.get(`education[${i}][dates]`)}</p>`;
  }

  // Skills section
  html += `<h2>Skills</h2><p>${formData.get('skills') || ''}</p>`;

  // Projects section
  html += `<h2>Projects</h2>`;
  for (let i = 0; ; i++) {
    if (!formData.get(`projects[${i}][title]`)) break;
    const bullets = formData.get(`projects[${i}][bullets]`);
    if (bullets) {
      const bulletList = bullets.split('|').map(b => "• " + b.trim()).join('<br>');
      html += `<p>${formData.get(`projects[${i}][title]`)} (${formData.get(`projects[${i}][dates]`)})<br>${bulletList}</p>`;
    }
  }

  // Work Experience section
  html += `<h2>Work Experience</h2>`;
  for (let i = 0; ; i++) {
    if (!formData.get(`work[${i}][title]`)) break;
    const bullets = formData.get(`work[${i}][bullets]`);
    if (bullets) {
      const bulletList = bullets.split('|').map(b => "• " + b.trim()).join('<br>');
      html += `<p>${formData.get(`work[${i}][title]`)} - ${formData.get(`work[${i}][location]`)} (${formData.get(`work[${i}][dates]`)})<br>${bulletList}</p>`;
    }
  }

  // Leadership Experience section
  html += `<h2>Leadership Experience</h2>`;
  for (let i = 0; ; i++) {
    if (!formData.get(`leadership[${i}][title]`)) break;
    const bullets = formData.get(`leadership[${i}][bullets]`);
    if (bullets) {
      const bulletList = bullets.split('|').map(b => "• " + b.trim()).join('<br>');
      html += `<p>${formData.get(`leadership[${i}][title]`)} - ${formData.get(`leadership[${i}][organization]`)} (${formData.get(`leadership[${i}][location]`)})<br>${bulletList}</p>`;
    }
  }

  preview.innerHTML = html;
}

// Wait for DOM to be fully loaded before attaching event listeners
document.addEventListener('DOMContentLoaded', function() {
  console.log('DOM loaded, initializing resume builder...');
  
  const form = document.getElementById('resumeForm');
  const previewBtn = document.getElementById('previewBtn');
  
  // Check if critical elements exist
  if (!form) {
    console.error('Resume form not found! Check your HTML.');
    return;
  }
  
  console.log('Form found, attaching listeners...');
  
  // Update preview on any input change
  form.addEventListener('input', updatePreview);
  
  // Preview button click handler
  if (previewBtn) {
    previewBtn.addEventListener('click', function(e) {
      e.preventDefault();
      updatePreview();
    });
  }
  
  // Generate initial preview
  updatePreview();

  // Form submission handler
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    console.log('Form submitted');
    
    const formData = new FormData(this);
    
    fetch('api.php', { 
      method: 'POST', 
      body: formData 
    })
    .then(res => {
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }
      return res.json();
    })
    .then(data => { 
      if (data.success) {
        alert('Resume saved! ID: ' + data.id);
        // Store ID for PDF download
        const resumeIdInput = document.getElementById('resumeId');
        if (resumeIdInput) {
          resumeIdInput.value = data.id;
        }
      } else {
        alert('Error: ' + (data.error || 'Unknown error'));
      }
    })
    .catch(err => {
      console.error('Fetch error:', err);
      alert('Error submitting form: ' + err.message);
    });
  });
  
  console.log('Resume builder initialized successfully!');
});