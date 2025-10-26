/**
 * Portfolio Management System - Frontend JavaScript
 * Uses Fetch API for AJAX operations
 * 
 * @author Portfolio Project - WMSU CCS
 * @version 1.0
 */

// API Base URL Configuration
const API_BASE_URL = 'http://localhost/portfolio_project/api';

// ============================================
// INITIALIZATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize navigation
    initNavigation();
    
    // Load initial data
    loadProfiles();
    loadSkills();
    loadProjects();
    loadHobbies();
    loadContacts();
});

/**
 * Initialize navigation tabs
 */
function initNavigation() {
    const navTabs = document.querySelectorAll('.nav-tab');
    
    navTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs and sections
            document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Show corresponding section
            const section = this.getAttribute('data-section');
            document.getElementById(`${section}-section`).classList.add('active');
        });
    });
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

/**
 * Show loading overlay
 */
function showLoading() {
    document.getElementById('loading-overlay').classList.add('active');
}

/**
 * Hide loading overlay
 */
function hideLoading() {
    document.getElementById('loading-overlay').classList.remove('active');
}

/**
 * Show toast notification
 */
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = `toast ${type} show`;
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

/**
 * Format date for display
 */
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

// ============================================
// PROFILE FUNCTIONS
// ============================================

/**
 * Load all profiles from API
 */
async function loadProfiles() {
    try {
        showLoading();
        const response = await fetch(`${API_BASE_URL}/profile_api.php`);
        const data = await response.json();
        
        if (data.status === 'success') {
            displayProfiles(data.data);
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error loading profiles:', error);
        showToast('Failed to load profiles', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Display profiles in cards grid
 */
function displayProfiles(profiles) {
    const container = document.getElementById('profile-container');
    
    if (profiles.length === 0) {
        container.innerHTML = '<p class="no-data">No profiles found. Click "Add Profile" to create one.</p>';
        return;
    }
    
    container.innerHTML = profiles.map(profile => `
        <div class="profile-card">
            <img src="${profile.photo_url || 'https://via.placeholder.com/150'}" 
                 alt="${profile.full_name}" class="profile-photo">
            <h3>${profile.full_name}</h3>
            <p class="profile-email"><i class="fas fa-envelope"></i> ${profile.email}</p>
            <p class="profile-phone"><i class="fas fa-phone"></i> ${profile.phone || 'N/A'}</p>
            <p class="profile-bio">${profile.bio || 'No bio available'}</p>
            
            <div class="profile-links">
                ${profile.linkedin_url ? `<a href="${profile.linkedin_url}" target="_blank"><i class="fab fa-linkedin"></i></a>` : ''}
                ${profile.github_url ? `<a href="${profile.github_url}" target="_blank"><i class="fab fa-github"></i></a>` : ''}
                ${profile.website_url ? `<a href="${profile.website_url}" target="_blank"><i class="fas fa-globe"></i></a>` : ''}
            </div>
            
            <div class="card-actions">
                <button class="btn btn-sm btn-warning" onclick="editProfile(${profile.id})">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteProfile(${profile.id})">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    `).join('');
}

/**
 * Open profile modal for adding
 */
function openProfileModal() {
    document.getElementById('profile-modal-title').textContent = 'Add Profile';
    document.getElementById('profile-form').reset();
    document.getElementById('profile-id').value = '';
    document.getElementById('profile-modal').classList.add('show');
}

/**
 * Close profile modal
 */
function closeProfileModal() {
    document.getElementById('profile-modal').classList.remove('show');
}

/**
 * Edit profile - load data into modal
 */
async function editProfile(id) {
    try {
        showLoading();
        const response = await fetch(`${API_BASE_URL}/profile_api.php?id=${id}`);
        const data = await response.json();
        
        if (data.status === 'success') {
            const profile = data.data;
            document.getElementById('profile-modal-title').textContent = 'Edit Profile';
            document.getElementById('profile-id').value = profile.id;
            document.getElementById('profile-full-name').value = profile.full_name;
            document.getElementById('profile-email').value = profile.email;
            document.getElementById('profile-phone').value = profile.phone || '';
            document.getElementById('profile-dob').value = profile.date_of_birth || '';
            document.getElementById('profile-address').value = profile.address || '';
            document.getElementById('profile-bio').value = profile.bio || '';
            document.getElementById('profile-photo').value = profile.photo_url || '';
            document.getElementById('profile-linkedin').value = profile.linkedin_url || '';
            document.getElementById('profile-github').value = profile.github_url || '';
            document.getElementById('profile-website').value = profile.website_url || '';
            
            document.getElementById('profile-modal').classList.add('show');
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error loading profile:', error);
        showToast('Failed to load profile', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Save profile (create or update)
 */
async function saveProfile(event) {
    event.preventDefault();
    
    const id = document.getElementById('profile-id').value;
    const profileData = {
        full_name: document.getElementById('profile-full-name').value,
        email: document.getElementById('profile-email').value,
        phone: document.getElementById('profile-phone').value,
        date_of_birth: document.getElementById('profile-dob').value,
        address: document.getElementById('profile-address').value,
        bio: document.getElementById('profile-bio').value,
        photo_url: document.getElementById('profile-photo').value,
        linkedin_url: document.getElementById('profile-linkedin').value,
        github_url: document.getElementById('profile-github').value,
        website_url: document.getElementById('profile-website').value
    };
    
    try {
        showLoading();
        
        let response;
        if (id) {
            // Update existing profile
            profileData.id = id;
            response = await fetch(`${API_BASE_URL}/profile_api.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(profileData)
            });
        } else {
            // Create new profile
            response = await fetch(`${API_BASE_URL}/profile_api.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(profileData)
            });
        }
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showToast(data.message, 'success');
            closeProfileModal();
            loadProfiles();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error saving profile:', error);
        showToast('Failed to save profile', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Delete profile with confirmation
 */
async function deleteProfile(id) {
    if (!confirm('Are you sure you want to delete this profile?')) {
        return;
    }
    
    try {
        showLoading();
        const response = await fetch(`${API_BASE_URL}/profile_api.php?id=${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showToast(data.message, 'success');
            loadProfiles();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error deleting profile:', error);
        showToast('Failed to delete profile', 'error');
    } finally {
        hideLoading();
    }
}

// ============================================
// SKILLS FUNCTIONS
// ============================================

/**
 * Load all skills from API
 */
async function loadSkills() {
    try {
        showLoading();
        
        // Load skills and statistics in parallel
        const [skillsResponse, statsResponse] = await Promise.all([
            fetch(`${API_BASE_URL}/skills_api.php`),
            fetch(`${API_BASE_URL}/skills_api.php?action=statistics`)
        ]);
        
        const skillsData = await skillsResponse.json();
        const statsData = await statsResponse.json();
        
        if (skillsData.status === 'success') {
            displaySkills(skillsData.data);
            
            // Populate category filter
            const categories = [...new Set(skillsData.data.map(s => s.category))];
            const filterSelect = document.getElementById('skills-filter');
            filterSelect.innerHTML = '<option value="">All Categories</option>';
            categories.forEach(cat => {
                filterSelect.innerHTML += `<option value="${cat}">${cat}</option>`;
            });
        }
        
        if (statsData.status === 'success') {
            displaySkillsStats(statsData.data);
        }
    } catch (error) {
        console.error('Error loading skills:', error);
        showToast('Failed to load skills', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Display skills statistics
 */
function displaySkillsStats(stats) {
    const container = document.getElementById('skills-stats');
    container.innerHTML = `
        <div class="stat-card">
            <i class="fas fa-code"></i>
            <h4>Total Skills</h4>
            <p class="stat-number">${stats.total_skills}</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-layer-group"></i>
            <h4>Categories</h4>
            <p class="stat-number">${stats.total_categories}</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-star"></i>
            <h4>Expert Skills</h4>
            <p class="stat-number">${stats.expert_count}</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-chart-line"></i>
            <h4>Avg Experience</h4>
            <p class="stat-number">${parseFloat(stats.avg_years_experience).toFixed(1)} yrs</p>
        </div>
    `;
}

/**
 * Display skills in table
 */
function displaySkills(skills) {
    const container = document.getElementById('skills-container');
    
    if (skills.length === 0) {
        container.innerHTML = '<p class="no-data">No skills found.</p>';
        return;
    }
    
    container.innerHTML = `
        <table class="data-table">
            <thead>
                <tr>
                    <th>Skill</th>
                    <th>Category</th>
                    <th>Proficiency</th>
                    <th>Experience</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                ${skills.map(skill => `
                    <tr>
                        <td>
                            ${skill.icon_class ? `<i class="${skill.icon_class}"></i>` : ''}
                            ${skill.skill_name}
                        </td>
                        <td><span class="badge">${skill.category}</span></td>
                        <td><span class="badge badge-${getProficiencyClass(skill.proficiency_level)}">${skill.proficiency_level}</span></td>
                        <td>${skill.years_of_experience} years</td>
                        <td class="action-buttons">
                            <button class="btn btn-sm btn-warning" onclick="editSkill(${skill.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteSkill(${skill.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

/**
 * Get proficiency CSS class
 */
function getProficiencyClass(level) {
    const classes = {
        'Beginner': 'info',
        'Intermediate': 'primary',
        'Advanced': 'warning',
        'Expert': 'success'
    };
    return classes[level] || 'secondary';
}

/**
 * Open skill modal for adding
 */
function openSkillModal() {
    document.getElementById('skill-modal-title').textContent = 'Add Skill';
    document.getElementById('skill-form').reset();
    document.getElementById('skill-id').value = '';
    document.getElementById('skill-modal').classList.add('show');
}

/**
 * Close skill modal
 */
function closeSkillModal() {
    document.getElementById('skill-modal').classList.remove('show');
}

/**
 * Edit skill
 */
async function editSkill(id) {
    try {
        showLoading();
        const response = await fetch(`${API_BASE_URL}/skills_api.php?id=${id}`);
        const data = await response.json();
        
        if (data.status === 'success') {
            const skill = data.data;
            document.getElementById('skill-modal-title').textContent = 'Edit Skill';
            document.getElementById('skill-id').value = skill.id;
            document.getElementById('skill-name').value = skill.skill_name;
            document.getElementById('skill-category').value = skill.category;
            document.getElementById('skill-proficiency').value = skill.proficiency_level;
            document.getElementById('skill-experience').value = skill.years_of_experience;
            document.getElementById('skill-description').value = skill.description || '';
            document.getElementById('skill-icon').value = skill.icon_class || '';
            
            document.getElementById('skill-modal').classList.add('show');
        }
    } catch (error) {
        console.error('Error loading skill:', error);
        showToast('Failed to load skill', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Save skill
 */
async function saveSkill(event) {
    event.preventDefault();
    
    const id = document.getElementById('skill-id').value;
    const skillData = {
        skill_name: document.getElementById('skill-name').value,
        category: document.getElementById('skill-category').value,
        proficiency_level: document.getElementById('skill-proficiency').value,
        years_of_experience: parseFloat(document.getElementById('skill-experience').value),
        description: document.getElementById('skill-description').value,
        icon_class: document.getElementById('skill-icon').value
    };
    
    try {
        showLoading();
        
        let response;
        if (id) {
            skillData.id = parseInt(id);
            response = await fetch(`${API_BASE_URL}/skills_api.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(skillData)
            });
        } else {
            response = await fetch(`${API_BASE_URL}/skills_api.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(skillData)
            });
        }
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showToast(data.message, 'success');
            closeSkillModal();
            loadSkills();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error saving skill:', error);
        showToast('Failed to save skill', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Delete skill
 */
async function deleteSkill(id) {
    if (!confirm('Are you sure you want to delete this skill?')) {
        return;
    }
    
    try {
        showLoading();
        const response = await fetch(`${API_BASE_URL}/skills_api.php?id=${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showToast(data.message, 'success');
            loadSkills();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error deleting skill:', error);
        showToast('Failed to delete skill', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Search skills
 */
function searchSkills() {
    const searchTerm = document.getElementById('skills-search').value.toLowerCase();
    const rows = document.querySelectorAll('#skills-container tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

/**
 * Filter skills by category
 */
function filterSkillsByCategory() {
    const category = document.getElementById('skills-filter').value;
    const rows = document.querySelectorAll('#skills-container tbody tr');
    
    rows.forEach(row => {
        if (!category) {
            row.style.display = '';
        } else {
            const rowCategory = row.querySelector('.badge').textContent;
            row.style.display = rowCategory === category ? '' : 'none';
        }
    });
}

// ============================================
// PROJECTS FUNCTIONS
// ============================================

/**
 * Load all projects from API
 */
async function loadProjects() {
    try {
        showLoading();
        
        const [projectsResponse, statsResponse] = await Promise.all([
            fetch(`${API_BASE_URL}/projects_api.php`),
            fetch(`${API_BASE_URL}/projects_api.php?action=statistics`)
        ]);
        
        const projectsData = await projectsResponse.json();
        const statsData = await statsResponse.json();
        
        if (projectsData.status === 'success') {
            displayProjects(projectsData.data);
        }
        
        if (statsData.status === 'success') {
            displayProjectsStats(statsData.data);
        }
    } catch (error) {
        console.error('Error loading projects:', error);
        showToast('Failed to load projects', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Display projects statistics
 */
function displayProjectsStats(stats) {
    const container = document.getElementById('projects-stats');
    container.innerHTML = `
        <div class="stat-card">
            <i class="fas fa-project-diagram"></i>
            <h4>Total Projects</h4>
            <p class="stat-number">${stats.total_projects}</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-check-circle"></i>
            <h4>Completed</h4>
            <p class="stat-number">${stats.completed_count}</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-spinner"></i>
            <h4>In Progress</h4>
            <p class="stat-number">${stats.in_progress_count}</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-star"></i>
            <h4>Featured</h4>
            <p class="stat-number">${stats.featured_count}</p>
        </div>
    `;
}

/**
 * Display projects in cards
 */
function displayProjects(projects) {
    const container = document.getElementById('projects-container');
    
    if (projects.length === 0) {
        container.innerHTML = '<p class="no-data">No projects found.</p>';
        return;
    }
    
    container.innerHTML = projects.map(project => `
        <div class="project-card ${project.featured ? 'featured' : ''}">
            ${project.featured ? '<span class="featured-badge"><i class="fas fa-star"></i> Featured</span>' : ''}
            <img src="${project.image_url || 'https://via.placeholder.com/400x250'}" 
                 alt="${project.project_title}" class="project-image">
            <div class="project-content">
                <h3>${project.project_title}</h3>
                <p class="project-description">${project.description}</p>
                
                ${project.technologies_used ? `
                    <div class="project-technologies">
                        ${project.technologies_used.split(',').map(tech => 
                            `<span class="tech-badge">${tech.trim()}</span>`
                        ).join('')}
                    </div>
                ` : ''}
                
                <div class="project-meta">
                    <span class="badge badge-${getStatusClass(project.status)}">${project.status}</span>
                    ${project.start_date ? `<span><i class="far fa-calendar"></i> ${formatDate(project.start_date)}</span>` : ''}
                </div>
                
                <div class="project-links">
                    ${project.project_url ? `<a href="${project.project_url}" target="_blank" class="btn btn-sm btn-primary">
                        <i class="fas fa-external-link-alt"></i> View Project
                    </a>` : ''}
                    ${project.github_url ? `<a href="${project.github_url}" target="_blank" class="btn btn-sm btn-secondary">
                        <i class="fab fa-github"></i> GitHub
                    </a>` : ''}
                </div>
                
                <div class="card-actions">
                    <button class="btn btn-sm btn-warning" onclick="editProject(${project.id})">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteProject(${project.id})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

/**
 * Get status CSS class
 */
function getStatusClass(status) {
    const classes = {
        'Planning': 'info',
        'In Progress': 'warning',
        'Completed': 'success',
        'Archived': 'secondary'
    };
    return classes[status] || 'secondary';
}

/**
 * Open project modal
 */
function openProjectModal() {
    document.getElementById('project-modal-title').textContent = 'Add Project';
    document.getElementById('project-form').reset();
    document.getElementById('project-id').value = '';
    document.getElementById('project-modal').classList.add('show');
}

/**
 * Close project modal
 */
function closeProjectModal() {
    document.getElementById('project-modal').classList.remove('show');
}

/**
 * Edit project
 */
async function editProject(id) {
    try {
        showLoading();
        const response = await fetch(`${API_BASE_URL}/projects_api.php?id=${id}`);
        const data = await response.json();
        
        if (data.status === 'success') {
            const project = data.data;
            document.getElementById('project-modal-title').textContent = 'Edit Project';
            document.getElementById('project-id').value = project.id;
            document.getElementById('project-title').value = project.project_title;
            document.getElementById('project-description').value = project.description;
            document.getElementById('project-technologies').value = project.technologies_used || '';
            document.getElementById('project-url').value = project.project_url || '';
            document.getElementById('project-github').value = project.github_url || '';
            document.getElementById('project-image').value = project.image_url || '';
            document.getElementById('project-start').value = project.start_date || '';
            document.getElementById('project-end').value = project.end_date || '';
            document.getElementById('project-status').value = project.status;
            document.getElementById('project-featured').checked = project.featured == 1;
            
            document.getElementById('project-modal').classList.add('show');
        }
    } catch (error) {
        console.error('Error loading project:', error);
        showToast('Failed to load project', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Save project
 */
async function saveProject(event) {
    event.preventDefault();
    
    const id = document.getElementById('project-id').value;
    const projectData = {
        project_title: document.getElementById('project-title').value,
        description: document.getElementById('project-description').value,
        technologies_used: document.getElementById('project-technologies').value,
        project_url: document.getElementById('project-url').value,
        github_url: document.getElementById('project-github').value,
        image_url: document.getElementById('project-image').value,
        start_date: document.getElementById('project-start').value,
        end_date: document.getElementById('project-end').value,
        status: document.getElementById('project-status').value,
        featured: document.getElementById('project-featured').checked
    };
    
    try {
        showLoading();
        
        let response;
        if (id) {
            projectData.id = parseInt(id);
            response = await fetch(`${API_BASE_URL}/projects_api.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(projectData)
            });
        } else {
            response = await fetch(`${API_BASE_URL}/projects_api.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(projectData)
            });
        }
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showToast(data.message, 'success');
            closeProjectModal();
            loadProjects();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error saving project:', error);
        showToast('Failed to save project', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Delete project
 */
async function deleteProject(id) {
    if (!confirm('Are you sure you want to delete this project?')) {
        return;
    }
    
    try {
        showLoading();
        const response = await fetch(`${API_BASE_URL}/projects_api.php?id=${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showToast(data.message, 'success');
            loadProjects();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error deleting project:', error);
        showToast('Failed to delete project', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Search projects
 */
function searchProjects() {
    const searchTerm = document.getElementById('projects-search').value.toLowerCase();
    const cards = document.querySelectorAll('.project-card');
    
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

/**
 * Filter projects by status
 */
function filterProjectsByStatus() {
    const status = document.getElementById('projects-filter').value;
    const cards = document.querySelectorAll('.project-card');
    
    cards.forEach(card => {
        if (!status) {
            card.style.display = '';
        } else {
            const cardStatus = card.querySelector('.badge').textContent;
            card.style.display = cardStatus === status ? '' : 'none';
        }
    });
}

// ============================================
// HOBBIES FUNCTIONS
// ============================================

/**
 * Load all hobbies
 */
async function loadHobbies() {
    try {
        showLoading();
        const response = await fetch(`${API_BASE_URL}/hobbies_api.php`);
        const data = await response.json();
        
        if (data.status === 'success') {
            displayHobbies(data.data);
        }
    } catch (error) {
        console.error('Error loading hobbies:', error);
        showToast('Failed to load hobbies', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Display hobbies
 */
function displayHobbies(hobbies) {
    const container = document.getElementById('hobbies-container');
    
    if (hobbies.length === 0) {
        container.innerHTML = '<p class="no-data">No hobbies found.</p>';
        return;
    }
    
    container.innerHTML = hobbies.map(hobby => `
        <div class="hobby-card">
            <div class="hobby-icon">
                <i class="${hobby.icon_class || 'fas fa-heart'}"></i>
            </div>
            <h3>${hobby.hobby_name}</h3>
            <p>${hobby.description || 'No description available'}</p>
            <div class="card-actions">
                <button class="btn btn-sm btn-warning" onclick="editHobby(${hobby.id})">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteHobby(${hobby.id})">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    `).join('');
}

/**
 * Open hobby modal
 */
function openHobbyModal() {
    document.getElementById('hobby-modal-title').textContent = 'Add Hobby';
    document.getElementById('hobby-form').reset();
    document.getElementById('hobby-id').value = '';
    document.getElementById('hobby-modal').classList.add('show');
}

/**
 * Close hobby modal
 */
function closeHobbyModal() {
    document.getElementById('hobby-modal').classList.remove('show');
}

/**
 * Edit hobby
 */
async function editHobby(id) {
    try {
        showLoading();
        const response = await fetch(`${API_BASE_URL}/hobbies_api.php?id=${id}`);
        const data = await response.json();
        
        if (data.status === 'success') {
            const hobby = data.data;
            document.getElementById('hobby-modal-title').textContent = 'Edit Hobby';
            document.getElementById('hobby-id').value = hobby.id;
            document.getElementById('hobby-name').value = hobby.hobby_name;
            document.getElementById('hobby-description').value = hobby.description || '';
            document.getElementById('hobby-icon').value = hobby.icon_class || '';
            
            document.getElementById('hobby-modal').classList.add('show');
        }
    } catch (error) {
        console.error('Error loading hobby:', error);
        showToast('Failed to load hobby', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Save hobby
 */
async function saveHobby(event) {
    event.preventDefault();
    
    const id = document.getElementById('hobby-id').value;
    const hobbyData = {
        hobby_name: document.getElementById('hobby-name').value,
        description: document.getElementById('hobby-description').value,
        icon_class: document.getElementById('hobby-icon').value
    };
    
    try {
        showLoading();
        
        let response;
        if (id) {
            hobbyData.id = parseInt(id);
            response = await fetch(`${API_BASE_URL}/hobbies_api.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(hobbyData)
            });
        } else {
            response = await fetch(`${API_BASE_URL}/hobbies_api.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(hobbyData)
            });
        }
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showToast(data.message, 'success');
            closeHobbyModal();
            loadHobbies();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error saving hobby:', error);
        showToast('Failed to save hobby', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Delete hobby
 */
async function deleteHobby(id) {
    if (!confirm('Are you sure you want to delete this hobby?')) {
        return;
    }
    
    try {
        showLoading();
        const response = await fetch(`${API_BASE_URL}/hobbies_api.php?id=${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showToast(data.message, 'success');
            loadHobbies();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error deleting hobby:', error);
        showToast('Failed to delete hobby', 'error');
    } finally {
        hideLoading();
    }
}

// ============================================
// CONTACTS FUNCTIONS
// ============================================

/**
 * Load all contacts
 */
async function loadContacts() {
    try {
        showLoading();
        
        const [contactsResponse, statsResponse] = await Promise.all([
            fetch(`${API_BASE_URL}/contacts_api.php`),
            fetch(`${API_BASE_URL}/contacts_api.php?action=statistics`)
        ]);
        
        const contactsData = await contactsResponse.json();
        const statsData = await statsResponse.json();
        
        if (contactsData.status === 'success') {
            displayContacts(contactsData.data);
        }
        
        if (statsData.status === 'success') {
            displayContactsStats(statsData.data);
        }
    } catch (error) {
        console.error('Error loading contacts:', error);
        showToast('Failed to load contacts', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Display contacts statistics
 */
function displayContactsStats(stats) {
    const container = document.getElementById('contacts-stats');
    container.innerHTML = `
        <div class="stat-card">
            <i class="fas fa-envelope"></i>
            <h4>Total Contacts</h4>
            <p class="stat-number">${stats.total_contacts}</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-bell"></i>
            <h4>New</h4>
            <p class="stat-number">${stats.new_count}</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-reply"></i>
            <h4>Replied</h4>
            <p class="stat-number">${stats.replied_count}</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-calendar-week"></i>
            <h4>This Week</h4>
            <p class="stat-number">${stats.last_week_count}</p>
        </div>
    `;
}

/**
 * Display contacts in table
 */
function displayContacts(contacts) {
    const container = document.getElementById('contacts-container');
    
    if (contacts.length === 0) {
        container.innerHTML = '<p class="no-data">No contacts found.</p>';
        return;
    }
    
    container.innerHTML = `
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                ${contacts.map(contact => `
                    <tr class="${contact.status === 'New' ? 'unread' : ''}">
                        <td>${contact.name}</td>
                        <td>${contact.email}</td>
                        <td>${contact.subject || 'N/A'}</td>
                        <td class="message-cell">${contact.message.substring(0, 50)}...</td>
                        <td>
                            <select class="status-select" onchange="updateContactStatus(${contact.id}, this.value)">
                                <option value="New" ${contact.status === 'New' ? 'selected' : ''}>New</option>
                                <option value="Read" ${contact.status === 'Read' ? 'selected' : ''}>Read</option>
                                <option value="Replied" ${contact.status === 'Replied' ? 'selected' : ''}>Replied</option>
                                <option value="Archived" ${contact.status === 'Archived' ? 'selected' : ''}>Archived</option>
                            </select>
                        </td>
                        <td>${formatDate(contact.created_at)}</td>
                        <td class="action-buttons">
                            <button class="btn btn-sm btn-danger" onclick="deleteContact(${contact.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

/**
 * Update contact status
 */
async function updateContactStatus(id, status) {
    try {
        showLoading();
        const response = await fetch(`${API_BASE_URL}/contacts_api.php?action=update_status`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, status })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showToast('Status updated successfully', 'success');
            loadContacts();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error updating status:', error);
        showToast('Failed to update status', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Delete contact
 */
async function deleteContact(id) {
    if (!confirm('Are you sure you want to delete this contact?')) {
        return;
    }
    
    try {
        showLoading();
        const response = await fetch(`${API_BASE_URL}/contacts_api.php?id=${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showToast(data.message, 'success');
            loadContacts();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error deleting contact:', error);
        showToast('Failed to delete contact', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Filter contacts by status
 */
function filterContactsByStatus() {
    const status = document.getElementById('contacts-filter').value;
    const rows = document.querySelectorAll('#contacts-container tbody tr');
    
    rows.forEach(row => {
        if (!status) {
            row.style.display = '';
        } else {
            const rowStatus = row.querySelector('.status-select').value;
            row.style.display = rowStatus === status ? '' : 'none';
        }
    });
}