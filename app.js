const nameEl = document.querySelector('#name');
const skillsEl = document.querySelector('#skills');
const interestsEl = document.querySelector('#interests');
const projectsEl = document.querySelector('#projects');
const descEl = document.querySelector('#description');
const githubLink = document.querySelector('#githubLink');
const emailLink = document.querySelector('#emailLink');

fetch('profile.json')
  .then((response) => {
    if (!response.ok) {
      throw new Error(`Chyba při načítání profile.json: ${response.status}`);
    }
    return response.json();
  })
  .then((data) => {
    nameEl.textContent = data.name;
    descEl.textContent = data.description || 'Žádný popis';

    skillsEl.innerHTML = '';
    data.skills.forEach((skill) => {
      const skillItem = document.createElement('li');
      skillItem.textContent = skill;
      skillsEl.appendChild(skillItem);
    });

    interestsEl.innerHTML = '';
    data.interests.forEach((interest) => {
      const p = document.createElement('p');
      p.textContent = `• ${interest}`;
      interestsEl.appendChild(p);
    });

    projectsEl.innerHTML = '';
    if (Array.isArray(data.projects) && data.projects.length > 0) {
      data.projects.forEach((project) => {
        const box = document.createElement('article');
        box.className = 'project';
        box.innerHTML = `
          <h4>${project.title}</h4>
          <p>${project.description}</p>
          <a href="${project.link}" target="_blank">Odkaz</a>
        `;
        projectsEl.appendChild(box);
      });
    } else {
      projectsEl.innerHTML = '<p>Žádné projekty k zobrazení.</p>';
    }

    githubLink.textContent = data.github;
    githubLink.href = data.github;
    emailLink.textContent = data.email;
    emailLink.href = `mailto:${data.email}`;
  })
  .catch((error) => {
    console.error(error);
    descEl.textContent = 'Nastala chyba při načítání dat. Zkus stránku obnovit.';
    skillsEl.innerHTML = '<li>Nelze načíst skills.</li>';
    interestsEl.innerHTML = '<p>Nelze načíst interests.</p>';
    projectsEl.innerHTML = '<p>Nelze načíst projects.</p>';
  });
