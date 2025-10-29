let eduCount = 1, projCount = 1, workCount = 1, leadCount = 1;

// Add dynamic sections
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

// Update preview dynamically
function updatePreview() {
  const form = document.getElementById('resumeForm');
  const preview = document.getElementById('preview');
  const formData = new FormData(form);

  let html = `<h1>${formData.get('name')||''}</h1>`;
  html += `<p>${formData.get('phone')||''} | ${formData.get('email')||''} | ${formData.get('linkedin')||''} | ${formData.get('website')||''}</p>`;
  html += `<h2>Objective</h2><p>${formData.get('objective')||''}</p>`;

  // Education
  html += `<h2>Education</h2>`;
  for(let i=0;;i++){
    if(!formData.get(`education[${i}][school]`)) break;
    html += `<p>${formData.get(`education[${i}][school]`)}<br>${formData.get(`education[${i}][degree]`)}<br>${formData.get(`education[${i}][dates]`)}</p>`;
  }

  // Skills
  html += `<h2>Skills</h2><p>${formData.get('skills')||''}</p>`;

  // Projects
  html += `<h2>Projects</h2>`;
  for(let i=0;;i++){
    if(!formData.get(`projects[${i}][title]`)) break;
    let bullets = formData.get(`projects[${i}][bullets]`).split('|').map(b=>"• "+b.trim()).join('<br>');
    html += `<p>${formData.get(`projects[${i}][title]`)} (${formData.get(`projects[${i}][dates]`)})<br>${bullets}</p>`;
  }

  // Work
  html += `<h2>Work Experience</h2>`;
  for(let i=0;;i++){
    if(!formData.get(`work[${i}][title]`)) break;
    let bullets = formData.get(`work[${i}][bullets]`).split('|').map(b=>"• "+b.trim()).join('<br>');
    html += `<p>${formData.get(`work[${i}][title]`)} - ${formData.get(`work[${i}][location]`)} (${formData.get(`work[${i}][dates]`)})<br>${bullets}</p>`;
  }

  // Leadership
  html += `<h2>Leadership Experience</h2>`;
  for(let i=0;;i++){
    if(!formData.get(`leadership[${i}][title]`)) break;
    let bullets = formData.get(`leadership[${i}][bullets]`).split('|').map(b=>"• "+b.trim()).join('<br>');
    html += `<p>${formData.get(`leadership[${i}][title]`)} - ${formData.get(`leadership[${i}][organization]`)} (${formData.get(`leadership[${i}][location]`)})<br>${bullets}</p>`;
  }

  preview.innerHTML = html;
}

// Attach event listeners after DOM loads
document.addEventListener('DOMContentLoaded', () => {
  // Preview updates whenever input changes
  document.getElementById('resumeForm').addEventListener('input', updatePreview);

  // Submit via AJAX
  document.getElementById('resumeForm').addEventListener('submit', function(e){
      e.preventDefault();
      const formData = new FormData(this);
      fetch('api.php', { method:'POST', body:formData })
        .then(res => res.json())
        .then(data => { 
          if(data.success){
            alert('Resume saved! ID: '+data.id);
            document.getElementById('resume_id').value = data.id; // store for PDF download
          } else alert('Error: '+data.error);
        })
        .catch(err => alert('Fetch error: '+err));
  });
});
