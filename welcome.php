<?php
session_start();

// Redirect to index if not authenticated
if (!isset($_SESSION['user_info'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user_info'];
$displayName = $user['name'] ?? $user['preferred_username'] ?? $user['email'] ?? 'User';
$initials = strtoupper(substr($displayName, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | IdentityHub PHP</title>
    <meta name="description" content="Welcome page for the IdentityHub PHP integration sample project.">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="container" style="max-width: 1000px;">
        <div class="glass-card" id="welcome-card" style="padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div class="avatar" style="width: 48px; height: 48px; font-size: 1.2rem;"><?php echo htmlspecialchars($initials); ?></div>
                    <div>
                        <h2 style="font-size: 1.2rem;"><?php echo htmlspecialchars($displayName); ?></h2>
                        <span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($user['email'] ?? 'Authenticated User'); ?></span>
                    </div>
                </div>
                <a href="logout.php" class="btn-logout" style="padding: 0.5rem 1.5rem; font-size: 0.9rem;">
                    Logout
                </a>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
                <aside>
                    <h3 style="font-size: 1rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 1rem;">Navigation</h3>
                    <nav style="display: flex; flex-direction: column; gap: 0.5rem;" id="dashboard-nav">
                        <a href="javascript:void(0)" onclick="showSection('contacts')" class="nav-link active" id="link-contacts">Contacts</a>
                        <a href="javascript:void(0)" onclick="showSection('dashboard')" class="nav-link" id="link-dashboard">Dashboard</a>
                        <a href="javascript:void(0)" onclick="showSection('profile')" class="nav-link" id="link-profile">Profile Details</a>
                        <a href="javascript:void(0)" onclick="showSection('security')" class="nav-link" id="link-security">Security Settings</a>
                    </nav>
                </aside>

                <main>
                    <!-- Contacts Section -->
                    <div id="section-contacts" class="content-section">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
                            <div>
                                <h1 style="font-size: 1.8rem; margin-bottom: 0.5rem; background: linear-gradient(to right, #fff, var(--text-muted)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Contact Directory</h1>
                                <p style="font-size: 0.95rem; margin-bottom: 0;">Manage your business connections. Changes persist in your local SQLite database.</p>
                            </div>
                            <button onclick="openAddContactModal()" class="btn-logout" style="background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%); display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; font-size: 0.9rem;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                Add Contact
                            </button>
                        </div>

                        <!-- Search Bar -->
                        <div style="margin-bottom: 1.5rem; position: relative;">
                            <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); display: flex; align-items: center;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            </span>
                            <input type="text" id="contact-search" placeholder="Search contacts by name, email, phone, company..." oninput="filterContacts()" class="glass-input" style="padding-left: 2.75rem;">
                        </div>

                        <!-- Contacts Grid -->
                        <div id="contacts-grid" class="contacts-grid">
                            <!-- Populated dynamically -->
                        </div>

                        <!-- Empty State -->
                        <div id="no-contacts" class="no-contacts-card" style="display: none;">
                            <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">📇</div>
                            <h3>No contacts found</h3>
                            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem; max-width: 300px; margin-left: auto; margin-right: auto;">Get started by adding your first connection details using the button above.</p>
                            <button onclick="openAddContactModal()" class="btn-logout" style="padding: 0.5rem 1.2rem; font-size: 0.85rem;">Create First Contact</button>
                        </div>
                    </div>

                    <!-- Dashboard Section -->
                    <div id="section-dashboard" class="content-section" style="display: none;">
                        <h1 style="font-size: 1.8rem; margin-bottom: 0.5rem;">Account Overview</h1>
                        <p style="font-size: 0.95rem; margin-bottom: 2rem;">Welcome back! Here is a summary of your identity claims from Operlity IdentityHub.</p>

                        <div class="claims-grid" style="grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));">
                            <?php 
                            $summaryClaims = ['name', 'email', 'preferred_username', 'role'];
                            foreach ($user as $key => $value): 
                                if (in_array($key, $summaryClaims) && (is_string($value) || is_numeric($value))):
                            ?>
                                <div class="claim-item">
                                    <span class="claim-label"><?php echo htmlspecialchars($key); ?></span>
                                    <span class="claim-value"><?php echo htmlspecialchars($value); ?></span>
                                </div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>

                    <!-- Profile Section -->
                    <div id="section-profile" class="content-section" style="display: none;">
                        <h1 style="font-size: 1.8rem; margin-bottom: 0.5rem;">Profile Details</h1>
                        <p style="font-size: 0.95rem; margin-bottom: 2rem;">Full list of OIDC claims received from the Identity provider.</p>
                        
                        <div style="background: rgba(255,255,255,0.02); border-radius: 12px; border: 1px solid var(--glass-border); overflow: hidden;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                                <thead>
                                    <tr style="background: rgba(255,255,255,0.05); text-align: left;">
                                        <th style="padding: 1rem; border-bottom: 1px solid var(--glass-border);">Claim Key</th>
                                        <th style="padding: 1rem; border-bottom: 1px solid var(--glass-border);">Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $excludedClaims = ['sub', 'tenant_id', 'iss', 'aud', 'exp', 'nbf', 'iat', 'auth_time', 'at_hash', 'c_hash', 'sid', 'jti', 'aio', 'uti'];
                                    foreach ($user as $key => $value): 
                                        if (in_array($key, $excludedClaims)) {
                                            continue;
                                        }
                                    ?>
                                    <tr>
                                        <td style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--glass-border); color: var(--accent); font-family: monospace;"><?php echo htmlspecialchars($key); ?></td>
                                        <td style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--glass-border); word-break: break-all;"><?php echo is_array($value) ? json_encode($value) : htmlspecialchars($value); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Security Section -->
                    <div id="section-security" class="content-section" style="display: none;">
                        <h1 style="font-size: 1.8rem; margin-bottom: 0.5rem;">Security Settings</h1>
                        <p style="font-size: 0.95rem; margin-bottom: 2rem;">Manage your active session and security preferences.</p>
                        
                        <div style="display: grid; gap: 1rem;">
                            <div class="claim-item" style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <span class="claim-label">Session ID</span>
                                    <span class="claim-value"><?php echo session_id(); ?></span>
                                </div>
                                <span style="background: #10b981; color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.7rem;">Active</span>
                            </div>
                            <div class="claim-item">
                                <span class="claim-label">Authentication Method</span>
                                <span class="claim-value">OIDC Authorization Code Flow with PKCE</span>
                            </div>
                            <div style="margin-top: 1rem; padding: 1.5rem; background: rgba(245, 87, 108, 0.05); border: 1px solid rgba(245, 87, 108, 0.2); border-radius: 12px;">
                                <h4 style="color: #f5576c; margin-bottom: 0.5rem;">Terminate Session</h4>
                                <p style="font-size: 0.85rem; margin-bottom: 1rem;">Clicking below will clear your local session and log you out from IdentityHub.</p>
                                <a href="logout.php" class="btn-logout" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">Sign Out Everywhere</a>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Add/Edit Contact Modal -->
    <div id="contact-modal" class="modal-overlay">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem;">
                <h2 id="modal-title" style="font-size: 1.3rem; margin-bottom: 0; background: none; -webkit-text-fill-color: initial; color: var(--text-main);">Add Contact</h2>
                <button onclick="closeContactModal()" style="background: none; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center;" class="btn-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <form id="contact-form" onsubmit="handleFormSubmit(event)">
                <input type="hidden" id="contact-id">
                
                <div class="form-group">
                    <label for="contact-name" class="form-label">Name *</label>
                    <input type="text" id="contact-name" required class="glass-input" placeholder="e.g. John Doe">
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="contact-email" class="form-label">Email</label>
                        <input type="email" id="contact-email" class="glass-input" placeholder="e.g. john@example.com">
                    </div>
                    <div class="form-group">
                        <label for="contact-phone" class="form-label">Phone</label>
                        <input type="text" id="contact-phone" class="glass-input" placeholder="e.g. +1 555-0199">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="contact-company" class="form-label">Company</label>
                        <input type="text" id="contact-company" class="glass-input" placeholder="e.g. Acme Corp">
                    </div>
                    <div class="form-group">
                        <label for="contact-job-title" class="form-label">Job Title</label>
                        <input type="text" id="contact-job-title" class="glass-input" placeholder="e.g. Product Manager">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; border-top: 1px solid var(--glass-border); padding-top: 1.5rem;">
                    <button type="button" onclick="closeContactModal()" class="btn-logout" style="background: rgba(255,255,255,0.05); color: var(--text-main); box-shadow: none; padding: 0.6rem 1.5rem;">Cancel</button>
                    <button type="submit" class="btn-logout" style="background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%); padding: 0.6rem 1.5rem;">Save Contact</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-confirm-modal" class="modal-overlay">
        <div class="modal-content" style="max-width: 400px; text-align: center;">
            <div style="color: #ef4444; margin-bottom: 1rem;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="filter: drop-shadow(0 0 8px rgba(239, 68, 68, 0.4));"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            </div>
            <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem;">Delete Contact?</h3>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 2rem;">Are you sure you want to delete this contact? This action is permanent and cannot be undone.</p>
            <div style="display: flex; justify-content: center; gap: 1rem;">
                <button onclick="closeDeleteConfirmModal()" class="btn-logout" style="background: rgba(255,255,255,0.05); color: var(--text-main); box-shadow: none; padding: 0.5rem 1.5rem; font-size: 0.85rem;">Cancel</button>
                <button onclick="confirmDelete()" class="btn-logout" style="background: #ef4444; box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3); padding: 0.5rem 1.5rem; font-size: 0.85rem;">Delete</button>
            </div>
        </div>
    </div>

    <!-- Toast Notifications Container -->
    <div id="toast-container" class="toast-container"></div>

    <style>
        .nav-link {
            color: var(--text-main);
            text-decoration: none;
            padding: 0.75rem;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .nav-link:hover {
            background: rgba(255,255,255,0.05);
        }
        .nav-link.active {
            color: var(--primary);
            font-weight: 600;
            background: rgba(99, 102, 241, 0.1);
        }

        /* Contacts Specific Styles */
        .contacts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        .contact-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 1.5rem;
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .contact-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(99, 102, 241, 0.4);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4), 0 0 15px rgba(99, 102, 241, 0.1);
        }
        .contact-avatar {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .contact-name {
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.25rem;
        }
        .contact-title-company {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 1.25rem;
            min-height: 1.2rem;
        }
        .contact-info-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .contact-info-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            word-break: break-all;
        }
        .contact-info-item svg {
            color: var(--accent);
            flex-shrink: 0;
        }
        .contact-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 1rem;
        }
        .glass-input {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: var(--text-main);
            padding: 0.75rem 1rem;
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            width: 100%;
        }
        .glass-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 10px rgba(99, 102, 241, 0.3);
            background: rgba(15, 23, 42, 0.8);
        }
        .form-group {
            margin-bottom: 1.25rem;
            text-align: left;
        }
        .form-label {
            display: block;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .btn-icon {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            color: var(--text-muted);
        }
        .btn-icon:hover {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-main);
            border-color: rgba(255, 255, 255, 0.2);
        }
        .btn-icon.edit:hover {
            color: var(--accent);
            border-color: rgba(6, 182, 212, 0.4);
            box-shadow: 0 0 10px rgba(6, 182, 212, 0.2);
        }
        .btn-icon.delete:hover {
            color: #ef4444;
            border-color: rgba(239, 68, 68, 0.4);
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.2);
        }
        .no-contacts-card {
            background: rgba(255, 255, 255, 0.01);
            border: 1px dashed var(--glass-border);
            border-radius: 20px;
            padding: 4rem 2rem;
            text-align: center;
            margin-top: 1.5rem;
        }
        .no-contacts-card h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        /* Modal overlay and modal transitions */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-content {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 2.5rem;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            transform: scale(0.9);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .modal-overlay.active .modal-content {
            transform: scale(1);
        }

        /* Toast Styling */
        .toast-container {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .toast {
            background: rgba(30, 41, 59, 0.9);
            border: 1px solid var(--glass-border);
            border-left: 4px solid var(--primary);
            border-radius: 10px;
            padding: 1rem 1.5rem;
            color: var(--text-main);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            min-width: 250px;
            animation: toastSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
        .toast.success {
            border-left-color: #10b981;
        }
        .toast.error {
            border-left-color: #ef4444;
        }
        @keyframes toastSlideIn {
            from { transform: translateX(50px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .toast.fade-out {
            animation: toastFadeOut 0.3s ease forwards;
        }
        @keyframes toastFadeOut {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.9); }
        }
    </style>

    <script>
        // Store all fetched contacts in memory for search filtering
        let contacts = [];
        let contactIdToDelete = null;

        const gradients = [
            'linear-gradient(135deg, #6366f1 0%, #a855f7 100%)', // indigo -> purple
            'linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%)', // cyan -> blue
            'linear-gradient(135deg, #10b981 0%, #059669 100%)', // emerald -> green
            'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', // amber -> orange
            'linear-gradient(135deg, #ec4899 0%, #f43f5e 100%)'  // pink -> rose
        ];

        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.style.display = 'none';
            });
            // Show target section
            document.getElementById('section-' + sectionId).style.display = 'block';
            
            // Update nav links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            document.getElementById('link-' + sectionId).classList.add('active');

            // Fetch contacts if section is contacts
            if (sectionId === 'contacts') {
                fetchContacts();
            }
        }

        // Fetch contacts from local SQLite database via PHP API
        async function fetchContacts() {
            try {
                const response = await fetch('contacts.php');
                if (!response.ok) throw new Error('Failed to load contacts');
                contacts = await response.json();
                renderContacts(contacts);
            } catch (err) {
                showToast(err.message || 'Error fetching contacts', 'error');
            }
        }

        // Render contacts list in the DOM
        function renderContacts(contactsList) {
            const grid = document.getElementById('contacts-grid');
            const emptyState = document.getElementById('no-contacts');
            
            grid.innerHTML = '';
            
            if (contactsList.length === 0) {
                grid.style.display = 'none';
                emptyState.style.display = 'block';
                return;
            }

            grid.style.display = 'grid';
            emptyState.style.display = 'none';

            contactsList.forEach(contact => {
                const initials = (contact.name || 'C').substring(0, 1).toUpperCase();
                
                // Pick deterministic gradient color based on name first letter
                const gradientIdx = (contact.name.charCodeAt(0) || 0) % gradients.length;
                const cardGradient = gradients[gradientIdx];

                let titleCompany = '';
                if (contact.job_title && contact.company) {
                    titleCompany = `${escapeHtml(contact.job_title)} at ${escapeHtml(contact.company)}`;
                } else if (contact.job_title) {
                    titleCompany = escapeHtml(contact.job_title);
                } else if (contact.company) {
                    titleCompany = escapeHtml(contact.company);
                }

                const cardHtml = `
                    <div class="contact-card" data-id="${contact.id}">
                        <div>
                            <div class="contact-avatar" style="background: ${cardGradient}">
                                ${escapeHtml(initials)}
                            </div>
                            <h3 class="contact-name">${escapeHtml(contact.name)}</h3>
                            <div class="contact-title-company">${titleCompany || '<span style="opacity: 0.3; font-style: italic;">No company details</span>'}</div>
                            
                            <div class="contact-info-list">
                                ${contact.email ? `
                                    <div class="contact-info-item">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                        <span>${escapeHtml(contact.email)}</span>
                                    </div>
                                ` : ''}
                                ${contact.phone ? `
                                    <div class="contact-info-item">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                        <span>${escapeHtml(contact.phone)}</span>
                                    </div>
                                ` : ''}
                            </div>
                        </div>

                        <div class="contact-actions">
                            <button onclick="openEditContactModal(${contact.id})" class="btn-icon edit" title="Edit Contact">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button onclick="openDeleteConfirmModal(${contact.id})" class="btn-icon delete" title="Delete Contact">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            </button>
                        </div>
                    </div>
                `;
                grid.insertAdjacentHTML('beforeend', cardHtml);
            });
        }

        // Live filter contacts from search bar
        function filterContacts() {
            const query = document.getElementById('contact-search').value.toLowerCase().trim();
            if (!query) {
                renderContacts(contacts);
                return;
            }

            const filtered = contacts.filter(contact => {
                return (contact.name && contact.name.toLowerCase().includes(query)) ||
                       (contact.email && contact.email.toLowerCase().includes(query)) ||
                       (contact.phone && contact.phone.toLowerCase().includes(query)) ||
                       (contact.company && contact.company.toLowerCase().includes(query)) ||
                       (contact.job_title && contact.job_title.toLowerCase().includes(query));
            });

            renderContacts(filtered);
        }

        // Modal triggers
        function openAddContactModal() {
            document.getElementById('modal-title').innerText = 'Add New Contact';
            document.getElementById('contact-id').value = '';
            document.getElementById('contact-form').reset();
            document.getElementById('contact-modal').classList.add('active');
        }

        function openEditContactModal(id) {
            const contact = contacts.find(c => c.id === id);
            if (!contact) return;

            document.getElementById('modal-title').innerText = 'Edit Contact';
            document.getElementById('contact-id').value = contact.id;
            document.getElementById('contact-name').value = contact.name || '';
            document.getElementById('contact-email').value = contact.email || '';
            document.getElementById('contact-phone').value = contact.phone || '';
            document.getElementById('contact-company').value = contact.company || '';
            document.getElementById('contact-job-title').value = contact.job_title || '';

            document.getElementById('contact-modal').classList.add('active');
        }

        function closeContactModal() {
            document.getElementById('contact-modal').classList.remove('active');
            document.getElementById('contact-form').reset();
        }

        // Handle Add/Edit Form submission
        async function handleFormSubmit(e) {
            e.preventDefault();

            const id = document.getElementById('contact-id').value;
            const name = document.getElementById('contact-name').value.trim();
            const email = document.getElementById('contact-email').value.trim();
            const phone = document.getElementById('contact-phone').value.trim();
            const company = document.getElementById('contact-company').value.trim();
            const job_title = document.getElementById('contact-job-title').value.trim();

            if (!name) {
                showToast('Name is required', 'error');
                return;
            }

            const payload = { id, name, email, phone, company, job_title };
            const action = id ? 'update' : 'create';

            try {
                const response = await fetch(`contacts.php?action=${action}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();
                
                if (!response.ok || result.error) {
                    throw new Error(result.error || 'Failed to save contact');
                }

                showToast(result.message || 'Contact saved successfully!');
                closeContactModal();
                fetchContacts(); // Reload contacts list
            } catch (err) {
                showToast(err.message, 'error');
            }
        }

        // Delete confirmation triggers
        function openDeleteConfirmModal(id) {
            contactIdToDelete = id;
            document.getElementById('delete-confirm-modal').classList.add('active');
        }

        function closeDeleteConfirmModal() {
            contactIdToDelete = null;
            document.getElementById('delete-confirm-modal').classList.remove('active');
        }

        async function confirmDelete() {
            if (!contactIdToDelete) return;

            try {
                const response = await fetch('contacts.php?action=delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: contactIdToDelete })
                });

                const result = await response.json();
                
                if (!response.ok || result.error) {
                    throw new Error(result.error || 'Failed to delete contact');
                }

                showToast(result.message || 'Contact deleted successfully!');
                closeDeleteConfirmModal();
                fetchContacts(); // Reload list
            } catch (err) {
                showToast(err.message, 'error');
            }
        }

        // Helper to show non-blocking feedback messages
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            const iconSvg = type === 'success' ? 
                `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981"><polyline points="20 6 9 17 4 12"></polyline></svg>` : 
                `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #ef4444"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>`;

            toast.innerHTML = `${iconSvg} <span>${escapeHtml(message)}</span>`;
            container.appendChild(toast);

            // Trigger animation and scheduled cleanup
            setTimeout(() => {
                toast.classList.add('fade-out');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        // Escape helper for basic XSS prevention in dynamic rendering
        function escapeHtml(str) {
            if (!str) return '';
            return str
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Initialize by fetching contacts when page loads
        document.addEventListener('DOMContentLoaded', () => {
            fetchContacts();
        });
    </script>
</body>
</html>
