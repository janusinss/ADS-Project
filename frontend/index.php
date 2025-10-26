<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Management System</title>
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <h1><i class="fas fa-briefcase"></i> Portfolio Management System</h1>
            <nav class="nav-tabs">
                <button class="nav-tab active" data-section="profile">
                    <i class="fas fa-user"></i> Profile
                </button>
                <button class="nav-tab" data-section="skills">
                    <i class="fas fa-code"></i> Skills
                </button>
                <button class="nav-tab" data-section="projects">
                    <i class="fas fa-project-diagram"></i> Projects
                </button>
                <button class="nav-tab" data-section="hobbies">
                    <i class="fas fa-heart"></i> Hobbies
                </button>
                <button class="nav-tab" data-section="contacts">
                    <i class="fas fa-envelope"></i> Contacts
                </button>
            </nav>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="container">
        
        <!-- ============================================ -->
        <!-- PROFILE SECTION -->
        <!-- ============================================ -->
        <section id="profile-section" class="content-section active">
            <div class="section-header">
                <h2><i class="fas fa-user"></i> Profile Management</h2>
                <button class="btn btn-primary" onclick="openProfileModal()">
                    <i class="fas fa-plus"></i> Add Profile
                </button>
            </div>

            <div id="profile-container" class="cards-grid"></div>
        </section>

        <!-- ============================================ -->
        <!-- SKILLS SECTION -->
        <!-- ============================================ -->
        <section id="skills-section" class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-code"></i> Skills Management</h2>
                <div class="header-actions">
                    <input type="text" id="skills-search" class="search-input" 
                           placeholder="Search skills..." onkeyup="searchSkills()">
                    <select id="skills-filter" class="filter-select" onchange="filterSkillsByCategory()">
                        <option value="">All Categories</option>
                    </select>
                    <button class="btn btn-primary" onclick="openSkillModal()">
                        <i class="fas fa-plus"></i> Add Skill
                    </button>
                </div>
            </div>

            <div id="skills-stats" class="stats-grid"></div>
            <div id="skills-container" class="table-container"></div>
        </section>

        <!-- ============================================ -->
        <!-- PROJECTS SECTION -->
        <!-- ============================================ -->
        <section id="projects-section" class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-project-diagram"></i> Projects Management</h2>
                <div class="header-actions">
                    <input type="text" id="projects-search" class="search-input" 
                           placeholder="Search projects..." onkeyup="searchProjects()">
                    <select id="projects-filter" class="filter-select" onchange="filterProjectsByStatus()">
                        <option value="">All Status</option>
                        <option value="Planning">Planning</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="Archived">Archived</option>
                    </select>
                    <button class="btn btn-primary" onclick="openProjectModal()">
                        <i class="fas fa-plus"></i> Add Project
                    </button>
                </div>
            </div>

            <div id="projects-stats" class="stats-grid"></div>
            <div id="projects-container" class="cards-grid"></div>
        </section>

        <!-- ============================================ -->
        <!-- HOBBIES SECTION -->
        <!-- ============================================ -->
        <section id="hobbies-section" class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-heart"></i> Hobbies Management</h2>
                <button class="btn btn-primary" onclick="openHobbyModal()">
                    <i class="fas fa-plus"></i> Add Hobby
                </button>
            </div>

            <div id="hobbies-container" class="cards-grid"></div>
        </section>

        <!-- ============================================ -->
        <!-- CONTACTS SECTION -->
        <!-- ============================================ -->
        <section id="contacts-section" class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-envelope"></i> Contacts Management</h2>
                <div class="header-actions">
                    <select id="contacts-filter" class="filter-select" onchange="filterContactsByStatus()">
                        <option value="">All Status</option>
                        <option value="New">New</option>
                        <option value="Read">Read</option>
                        <option value="Replied">Replied</option>
                        <option value="Archived">Archived</option>
                    </select>
                </div>
            </div>

            <div id="contacts-stats" class="stats-grid"></div>
            <div id="contacts-container" class="table-container"></div>
        </section>

    </main>

    <!-- ============================================ -->
    <!-- PROFILE MODAL -->
    <!-- ============================================ -->
    <div id="profile-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user"></i> <span id="profile-modal-title">Add Profile</span></h3>
                <span class="close" onclick="closeProfileModal()">&times;</span>
            </div>
            <form id="profile-form" onsubmit="saveProfile(event)">
                <input type="hidden" id="profile-id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="profile-full-name">Full Name *</label>
                        <input type="text" id="profile-full-name" required>
                    </div>
                    <div class="form-group">
                        <label for="profile-email">Email *</label>
                        <input type="email" id="profile-email" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="profile-phone">Phone</label>
                        <input type="tel" id="profile-phone">
                    </div>
                    <div class="form-group">
                        <label for="profile-dob">Date of Birth</label>
                        <input type="date" id="profile-dob">
                    </div>
                </div>

                <div class="form-group">
                    <label for="profile-address">Address</label>
                    <input type="text" id="profile-address">
                </div>

                <div class="form-group">
                    <label for="profile-bio">Bio</label>
                    <textarea id="profile-bio" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label for="profile-photo">Photo URL</label>
                    <input type="url" id="profile-photo">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="profile-linkedin">LinkedIn URL</label>
                        <input type="url" id="profile-linkedin">
                    </div>
                    <div class="form-group">
                        <label for="profile-github">GitHub URL</label>
                        <input type="url" id="profile-github">
                    </div>
                </div>

                <div class="form-group">
                    <label for="profile-website">Website URL</label>
                    <input type="url" id="profile-website">
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeProfileModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- SKILL MODAL -->
    <!-- ============================================ -->
    <div id="skill-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-code"></i> <span id="skill-modal-title">Add Skill</span></h3>
                <span class="close" onclick="closeSkillModal()">&times;</span>
            </div>
            <form id="skill-form" onsubmit="saveSkill(event)">
                <input type="hidden" id="skill-id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="skill-name">Skill Name *</label>
                        <input type="text" id="skill-name" required>
                    </div>
                    <div class="form-group">
                        <label for="skill-category">Category *</label>
                        <input type="text" id="skill-category" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="skill-proficiency">Proficiency Level *</label>
                        <select id="skill-proficiency" required>
                            <option value="">Select Level</option>
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                            <option value="Expert">Expert</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="skill-experience">Years of Experience</label>
                        <input type="number" id="skill-experience" min="0" step="0.5" value="0">
                    </div>
                </div>

                <div class="form-group">
                    <label for="skill-description">Description</label>
                    <textarea id="skill-description" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label for="skill-icon">Icon Class (Font Awesome)</label>
                    <input type="text" id="skill-icon" placeholder="e.g., fab fa-php">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeSkillModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Skill
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- PROJECT MODAL -->
    <!-- ============================================ -->
    <div id="project-modal" class="modal">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3><i class="fas fa-project-diagram"></i> <span id="project-modal-title">Add Project</span></h3>
                <span class="close" onclick="closeProjectModal()">&times;</span>
            </div>
            <form id="project-form" onsubmit="saveProject(event)">
                <input type="hidden" id="project-id">
                
                <div class="form-group">
                    <label for="project-title">Project Title *</label>
                    <input type="text" id="project-title" required>
                </div>

                <div class="form-group">
                    <label for="project-description">Description *</label>
                    <textarea id="project-description" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label for="project-technologies">Technologies Used</label>
                    <input type="text" id="project-technologies" placeholder="e.g., PHP, MySQL, JavaScript">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="project-url">Project URL</label>
                        <input type="url" id="project-url">
                    </div>
                    <div class="form-group">
                        <label for="project-github">GitHub URL</label>
                        <input type="url" id="project-github">
                    </div>
                </div>

                <div class="form-group">
                    <label for="project-image">Image URL</label>
                    <input type="url" id="project-image">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="project-start">Start Date</label>
                        <input type="date" id="project-start">
                    </div>
                    <div class="form-group">
                        <label for="project-end">End Date</label>
                        <input type="date" id="project-end">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="project-status">Status *</label>
                        <select id="project-status" required>
                            <option value="">Select Status</option>
                            <option value="Planning">Planning</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Archived">Archived</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="project-featured">
                            <input type="checkbox" id="project-featured">
                            Featured Project
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeProjectModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Project
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- HOBBY MODAL -->
    <!-- ============================================ -->
    <div id="hobby-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-heart"></i> <span id="hobby-modal-title">Add Hobby</span></h3>
                <span class="close" onclick="closeHobbyModal()">&times;</span>
            </div>
            <form id="hobby-form" onsubmit="saveHobby(event)">
                <input type="hidden" id="hobby-id">
                
                <div class="form-group">
                    <label for="hobby-name">Hobby Name *</label>
                    <input type="text" id="hobby-name" required>
                </div>

                <div class="form-group">
                    <label for="hobby-description">Description</label>
                    <textarea id="hobby-description" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label for="hobby-icon">Icon Class (Font Awesome)</label>
                    <input type="text" id="hobby-icon" placeholder="e.g., fas fa-music">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeHobbyModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Hobby
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast"></div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="loading-overlay">
        <div class="spinner"></div>
    </div>

    <!-- JavaScript -->
    <script src="portfolio.js"></script>
</body>
</html>