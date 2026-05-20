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
                        <a href="javascript:void(0)" onclick="showSection('dashboard')" class="nav-link active" id="link-dashboard">Dashboard</a>
                        <a href="javascript:void(0)" onclick="showSection('profile')" class="nav-link" id="link-profile">Profile Details</a>
                        <a href="javascript:void(0)" onclick="showSection('security')" class="nav-link" id="link-security">Security Settings</a>
                    </nav>
                </aside>

                <main>
                    <!-- Dashboard Section -->
                    <div id="section-dashboard" class="content-section">
                        <h1 style="font-size: 1.8rem; margin-bottom: 0.5rem;">Account Overview</h1>
                        <p style="font-size: 0.95rem; margin-bottom: 2rem;">Welcome back! Here is a summary of your identity claims from Operlity IdentityHub.</p>

                        <div class="claims-grid" style="grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));">
                            <?php 
                            $summaryClaims = ['sub', 'name', 'email', 'preferred_username', 'role', 'tenant_id'];
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
                                    <?php foreach ($user as $key => $value): ?>
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
    </style>

    <script>
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
        }
    </script>
</body>
</html>
