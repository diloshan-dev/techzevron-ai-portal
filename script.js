/* =============================================
   TechZevron - JavaScript Functions
   ============================================= */

// IMMEDIATE Theme Loading - Prevent flash
(function() {
    var savedTheme = localStorage.getItem('techzevron_theme') || 'midnight';
    document.body.setAttribute('data-theme', savedTheme);
})();

// Theme System
function applyTheme(themeName) {
    document.body.setAttribute('data-theme', themeName);
    localStorage.setItem('techzevron_theme', themeName);
    
    document.querySelectorAll('.theme-option').forEach(function(opt) {
        opt.classList.remove('active');
    });
    var themeEl = document.querySelector('[data-theme="' + themeName + '"]');
    if (themeEl) themeEl.classList.add('active');
    
    closeThemeModal();
    showToast('Theme: ' + themeName.charAt(0).toUpperCase() + themeName.slice(1));
}

function loadTheme() {
    var savedTheme = localStorage.getItem('techzevron_theme') || 'midnight';
    document.body.setAttribute('data-theme', savedTheme);
}

function openThemeModal() {
    var modal = document.getElementById('themeModal');
    if (modal) {
        modal.style.display = 'flex';
    }
    closeSidebar();
}

function closeThemeModal() {
    var modal = document.getElementById('themeModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Sidebar Toggle
function toggleSidebar() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var menuToggle = document.getElementById('menuToggle');
    
    if (sidebar) sidebar.classList.toggle('active');
    if (overlay) overlay.classList.toggle('active');
    if (menuToggle) menuToggle.classList.toggle('active');
    
    var overflow = document.body.style.overflow;
    document.body.style.overflow = (overflow === 'hidden') ? '' : 'hidden';
}

function closeSidebar() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var menuToggle = document.getElementById('menuToggle');
    
    if (sidebar) {
        sidebar.classList.remove('active');
        sidebar.style.right = '-80%';
    }
    if (overlay) {
        overlay.classList.remove('active');
        overlay.style.opacity = '0';
        overlay.style.visibility = 'hidden';
    }
    if (menuToggle) menuToggle.classList.remove('active');
    document.body.style.overflow = '';
}

// Auto-close sidebar when clicking sidebar menu links
document.addEventListener('DOMContentLoaded', function() {
    var sidebarLinks = document.querySelectorAll('.sidebar-menu a');
    sidebarLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            closeSidebar();
        });
    });
});

// Close sidebar on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSidebar();
        closeThemeModal();
    }
});

// Toast Notification
function showToast(message) {
    var existingToast = document.querySelector('.toast-notification');
    if (existingToast) existingToast.remove();
    
    var toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
    document.body.appendChild(toast);
    
    // Trigger animation
    setTimeout(function() { toast.classList.add('show'); }, 10);
    
    // Remove after 3 seconds
    setTimeout(function() {
        toast.classList.remove('show');
        setTimeout(function() { toast.remove(); }, 300);
    }, 3000);
}

// Engagement Functions
function toggleLike(resourceId) {
    var formData = new FormData();
    formData.append('action', 'toggle_like');
    formData.append('resource_id', resourceId);
    
    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            var likeBtn = document.querySelector('[data-resource="' + resourceId + '"] .like-btn');
            var likeCount = document.querySelector('[data-resource="' + resourceId + '"] .like-count');
            
            if (likeBtn) {
                likeBtn.classList.toggle('active', data.liked);
                likeBtn.innerHTML = data.liked ? '<i class="fas fa-heart"></i>' : '<i class="far fa-heart"></i>';
            }
            if (likeCount) {
                likeCount.textContent = data.like_count;
            }
            
            showToast(data.liked ? 'Liked!' : 'Unliked');
        }
    });
}

function openComments(resourceId) {
    var modal = document.getElementById('commentsModal');
    if (modal) {
        modal.style.display = 'flex';
        loadComments(resourceId);
    }
}

function closeCommentsModal() {
    var modal = document.getElementById('commentsModal');
    if (modal) modal.style.display = 'none';
}

function loadComments(resourceId) {
    var commentsContainer = document.getElementById('commentsContainer');
    if (!commentsContainer) return;
    
    var formData = new FormData();
    formData.append('action', 'get_comments');
    formData.append('resource_id', resourceId);
    
    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            commentsContainer.innerHTML = '';
            
            if (data.comments && data.comments.length > 0) {
                data.comments.forEach(function(comment) {
                    var commentDiv = document.createElement('div');
                    commentDiv.className = 'comment-item';
                    commentDiv.innerHTML = 
                        '<div class="comment-avatar">' +
                            '<img src="' + (comment.profile_image || 'https://via.placeholder.com/40') + '" alt="">' +
                        '</div>' +
                        '<div class="comment-content">' +
                            '<div class="comment-header">' +
                                '<span class="commenter-name">' + comment.commenter_name + '</span>' +
                                '<span class="comment-time">' + comment.created_at + '</span>' +
                            '</div>' +
                            '<p class="comment-text">' + comment.comment_text + '</p>' +
                        '</div>';
                    commentsContainer.appendChild(commentDiv);
                });
            } else {
                commentsContainer.innerHTML = '<p class="no-comments">No comments yet. Be the first to comment!</p>';
            }
        }
    });
}

function submitComment(resourceId) {
    var commentInput = document.getElementById('commentInput');
    var commentText = commentInput.value.trim();
    
    if (!commentText) {
        showToast('Please enter a comment');
        return;
    }
    
    var formData = new FormData();
    formData.append('action', 'add_comment');
    formData.append('resource_id', resourceId);
    formData.append('comment', commentText);
    
    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            commentInput.value = '';
            showToast('Comment added!');
            loadComments(resourceId);
        } else {
            showToast(data.message || 'Failed to add comment');
        }
    });
}

// Likes Modal Functions
function openLikesModal(resourceId) {
    var modal = document.getElementById('likesModal');
    if (modal) {
        modal.style.display = 'flex';
        loadLikes(resourceId);
    }
}

function loadLikes(resourceId) {
    var likesList = document.getElementById('likesList');
    if (!likesList) return;
    
    var formData = new FormData();
    formData.append('action', 'get_likes');
    formData.append('resource_id', resourceId);
    
    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success && data.likes) {
            likesList.innerHTML = '';
            
            if (data.likes.length > 0) {
                data.likes.forEach(function(like) {
                    var likeDiv = document.createElement('div');
                    likeDiv.className = 'like-item';
                    likeDiv.innerHTML = 
                        '<img src="' + (like.profile_image || 'https://via.placeholder.com/40') + '" alt="">' +
                        '<span>' + like.user_name + '</span>';
                    likesList.appendChild(likeDiv);
                });
            } else {
                likesList.innerHTML = '<p class="no-likes">No likes yet</p>';
            }
        }
    });
}

function closeLikesModal() {
    var modal = document.getElementById('likesModal');
    if (modal) modal.style.display = 'none';
}

// Report Functions
function toggleReport(resourceId) {
    var modal = document.getElementById('reportModal');
    var resourceIdInput = document.getElementById('reportResourceId');
    
    if (modal) modal.style.display = 'flex';
    if (resourceIdInput) resourceIdInput.value = resourceId;
}

function closeReportModal() {
    var modal = document.getElementById('reportModal');
    if (modal) modal.style.display = 'none';
}

function submitReport() {
    var resourceId = document.getElementById('reportResourceId').value;
    var reason = document.getElementById('reportReason').value;
    
    var formData = new FormData();
    formData.append('action', 'add_report');
    formData.append('resource_id', resourceId);
    formData.append('reason', reason);
    
    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        alert(data.message);
        if (data.success) {
            closeReportModal();
            showToast('Report submitted!');
        }
    });
}

// Profile Image Preview
function previewProfileImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById('profilePreview');
            if (preview) preview.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Country Select - Show/Hide District
function toggleDistrict() {
    var countrySelect = document.getElementById('country');
    var districtGroup = document.getElementById('districtGroup');
    
    if (countrySelect && districtGroup) {
        districtGroup.style.display = (countrySelect.value === 'Sri Lanka') ? 'block' : 'none';
        if (countrySelect.value !== 'Sri Lanka') {
            var districtInput = document.getElementById('district');
            if (districtInput) districtInput.value = '';
        }
    }
}

// Password Strength
function checkPasswordStrength(password) {
    var strengthBar = document.querySelector('.password-strength-bar');
    if (!strengthBar) return;
    
    var strength = 0;
    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    var colors = ['#ef4444', '#f97316', '#eab308', '#22c55e', '#10b981'];
    var width = (strength / 5) * 100;
    
    strengthBar.style.width = width + '%';
    strengthBar.style.backgroundColor = colors[Math.min(strength - 1, 4)] || colors[0];
}

// Scroll Reveal Animation
function reveal() {
    var reveals = document.querySelectorAll('.reveal');
    for (var i = 0; i < reveals.length; i++) {
        var windowHeight = window.innerHeight;
        var elementTop = reveals[i].getBoundingClientRect().top;
        var elementVisible = 150;
        if (elementTop < windowHeight - elementVisible) {
            reveals[i].classList.add('active');
        }
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadTheme();
    reveal();
});

// Add scroll listener for reveal
window.addEventListener('scroll', reveal);
