/* =============================================
   TechZevron - JavaScript Functions
   ============================================= */

// Theme System
function applyTheme(themeName) {
    document.body.setAttribute('data-theme', themeName);
    localStorage.setItem('techzevron_theme', themeName);
    
    document.querySelectorAll('.theme-option').forEach(opt => {
        opt.classList.remove('active');
    });
    document.querySelector(`[data-theme="${themeName}"]`)?.classList.add('active');
    
    closeThemeModal();
    showToast('Theme: ' + themeName.charAt(0).toUpperCase() + themeName.slice(1));
}

function loadTheme() {
    const savedTheme = localStorage.getItem('techzevron_theme') || 'classic';
    document.body.setAttribute('data-theme', savedTheme);
}

function openThemeModal() {
    const modal = document.getElementById('themeModal');
    if (modal) {
        modal.style.display = 'flex';
    }
    closeSidebar();
}

function closeThemeModal() {
    const modal = document.getElementById('themeModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Sidebar Toggle
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const menuToggle = document.getElementById('menuToggle');
    
    if (sidebar) sidebar.classList.toggle('active');
    if (overlay) overlay.classList.toggle('active');
    if (menuToggle) menuToggle.classList.toggle('active');
    
    document.body.style.overflow = document.body.style.overflow === 'hidden' ? '' : 'hidden';
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const menuToggle = document.getElementById('menuToggle');
    
    if (sidebar) sidebar.classList.remove('active');
    if (overlay) overlay.classList.remove('active');
    if (menuToggle) menuToggle.classList.remove('active');
    document.body.style.overflow = '';
}

// Close sidebar on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSidebar();
        closeThemeModal();
    }
});

// Toast Notification
function showToast(message) {
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) existingToast.remove();
    
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Scroll Reveal Animation
function reveal() {
    const reveals = document.querySelectorAll('.reveal');
    const windowHeight = window.innerHeight;
    const elementVisible = 150;
    
    reveals.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        if (elementTop < windowHeight - elementVisible) {
            element.classList.add('active');
        }
    });
}

window.addEventListener('scroll', reveal);

document.addEventListener('DOMContentLoaded', function() {
    loadTheme();
    reveal();
    
    const menuToggle = document.getElementById('menuToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarClose = document.getElementById('sidebarClose');
    
    menuToggle?.addEventListener('click', toggleSidebar);
    sidebarOverlay?.addEventListener('click', closeSidebar);
    sidebarClose?.addEventListener('click', closeSidebar);
    
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    };
});

// Category Filter
const filterBtns = document.querySelectorAll('.filter-btn');
if (filterBtns.length > 0) {
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            const category = btn.dataset.category;
            const toolCards = document.querySelectorAll('.tool-card');
            
            toolCards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.5s ease-out';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

// Modal Functions
function openAddModal() {
    const modal = document.getElementById('resourceModal');
    if (modal) {
        modal.style.display = 'flex';
        document.getElementById('modalTitle').textContent = 'Add New Prompt';
        document.getElementById('resourceId').value = '';
        document.getElementById('resourceForm').reset();
        document.getElementById('modalSubmitBtn').innerHTML = '<i class="fas fa-plus"></i> Add Resource';
    }
}

function closeModal() {
    const modal = document.getElementById('resourceModal');
    if (modal) modal.style.display = 'none';
}

function editResource(id, title, description, prompt_text, category, image_url) {
    const modal = document.getElementById('resourceModal');
    if (modal) {
        modal.style.display = 'flex';
        document.getElementById('modalTitle').textContent = 'Edit Prompt';
        document.getElementById('resourceId').value = id;
        document.getElementById('title').value = title;
        document.getElementById('description').value = description;
        document.getElementById('prompt_text').value = prompt_text;
        document.getElementById('category').value = category;
        document.getElementById('image_url').value = image_url;
        document.getElementById('modalSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Update Resource';
    }
}

// Form Submit Handler
const resourceForm = document.getElementById('resourceForm');
if (resourceForm) {
    resourceForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = document.getElementById('resourceId').value;
        const formData = new FormData();
        
        if (id) {
            formData.append('action', 'update_resource');
            formData.append('id', id);
        } else {
            formData.append('action', 'add_resource');
        }
        
        formData.append('title', document.getElementById('title').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('prompt_text', document.getElementById('prompt_text').value);
        formData.append('category', document.getElementById('category').value);
        formData.append('image_url', document.getElementById('image_url').value);
        
        fetch('api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                closeModal();
                location.reload();
            }
        });
    });
}

// Delete Resource
function deleteResource(id) {
    if (confirm('Are you sure you want to delete this resource?')) {
        const formData = new FormData();
        formData.append('action', 'delete_resource');
        formData.append('id', id);
        
        fetch('api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) location.reload();
        });
    }
}

// Like Function
function toggleLike(resourceId) {
    const formData = new FormData();
    formData.append('action', 'toggle_like');
    formData.append('resource_id', resourceId);
    
    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const likeBtn = document.querySelector(`[onclick="toggleLike(${resourceId})"]`);
            const likeCount = likeBtn.querySelector('.like-count');
            const heartIcon = likeBtn.querySelector('i');
            
            likeCount.textContent = data.like_count;
            if (data.liked) {
                likeBtn.classList.add('liked');
                heartIcon.classList.remove('far');
                heartIcon.classList.add('fas');
            } else {
                likeBtn.classList.remove('liked');
                heartIcon.classList.remove('fas');
                heartIcon.classList.add('far');
            }
        } else {
            alert(data.message);
        }
    });
}

// Comment Functions
function toggleComment(resourceId) {
    const commentSection = document.getElementById('comment-section-' + resourceId);
    if (commentSection.style.display === 'none') {
        commentSection.style.display = 'block';
        loadComments(resourceId);
    } else {
        commentSection.style.display = 'none';
    }
}

function loadComments(resourceId) {
    const formData = new FormData();
    formData.append('action', 'get_comments');
    formData.append('resource_id', resourceId);
    
    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const commentList = document.getElementById('comment-list-' + resourceId);
            if (data.comments.length === 0) {
                commentList.innerHTML = '<p class="no-comments">No comments yet. Be the first!</p>';
            } else {
                commentList.innerHTML = data.comments.map(comment => `
                    <div class="comment-item">
                        <div class="comment-avatar">
                            ${comment.profile_image ? 
                                `<img src="${comment.profile_image}" alt="${comment.user_name}">` :
                                '<i class="fas fa-user"></i>'
                            }
                        </div>
                        <div class="comment-content">
                            <div class="comment-header">
                                <strong>${comment.user_name || 'User'} ${comment.is_fake ? '<span class="fake-badge"><i class="fas fa-user-secret"></i></span>' : ''}</strong>
                                <span class="comment-time">${new Date(comment.created_at).toLocaleDateString()}</span>
                                ${data.is_admin ? `
                                    <button class="comment-edit-btn" onclick="editComment(${comment.id}, '${encodeURIComponent(comment.comment_text)}', ${resourceId})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="comment-delete-btn" onclick="deleteComment(${comment.id}, ${resourceId})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                ` : ''}
                            </div>
                            <p>${comment.comment_text}</p>
                        </div>
                    </div>
                `).join('');
            }
        }
    });
}

function submitComment(resourceId) {
    const commentText = document.getElementById('comment-text-' + resourceId).value.trim();
    
    if (!commentText) {
        alert('Please write a comment');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'add_comment');
    formData.append('resource_id', resourceId);
    formData.append('comment_text', commentText);
    
    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('comment-text-' + resourceId).value = '';
            const commentBtn = document.querySelector(`[onclick="toggleComment(${resourceId})"]`);
            const commentCount = commentBtn.querySelector('.comment-count');
            commentCount.textContent = data.comment_count;
            loadComments(resourceId);
            showToast('Comment added!');
        } else {
            alert(data.message);
        }
    });
}

// Admin: Fake Comment
function addFakeComment(resourceId) {
    const name = document.getElementById('fake-name-' + resourceId).value.trim();
    const email = document.getElementById('fake-email-' + resourceId).value.trim();
    const comment = document.getElementById('fake-comment-' + resourceId).value.trim();
    
    if (!name || !email || !comment) {
        alert('Please fill all fields');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'add_fake_comment');
    formData.append('resource_id', resourceId);
    formData.append('name', name);
    formData.append('email', email);
    formData.append('comment_text', comment);
    
    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            document.getElementById('fake-name-' + resourceId).value = '';
            document.getElementById('fake-email-' + resourceId).value = '';
            document.getElementById('fake-comment-' + resourceId).value = '';
            const commentBtn = document.querySelector(`[onclick="toggleComment(${resourceId})"]`);
            const commentCount = commentBtn.querySelector('.comment-count');
            commentCount.textContent = data.comment_count;
            loadComments(resourceId);
        }
    });
}

// Admin: Edit Comment
function editComment(commentId, text, resourceId) {
    const newText = prompt('Edit comment:', decodeURIComponent(text));
    if (newText === null) return;
    
    const formData = new FormData();
    formData.append('action', 'update_comment');
    formData.append('comment_id', commentId);
    formData.append('comment_text', newText);
    
    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) loadComments(resourceId);
    });
}

// Admin: Delete Comment
function deleteComment(commentId, resourceId) {
    if (!confirm('Delete this comment?')) return;
    
    const formData = new FormData();
    formData.append('action', 'delete_comment');
    formData.append('comment_id', commentId);
    formData.append('resource_id', resourceId);
    
    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            const commentBtn = document.querySelector(`[onclick="toggleComment(${resourceId})"]`);
            const commentCount = commentBtn.querySelector('.comment-count');
            commentCount.textContent = data.comment_count;
            loadComments(resourceId);
        }
    });
}

// Admin: View Likes
function viewLikes(resourceId) {
    const modal = document.getElementById('likesModal');
    if (modal) modal.style.display = 'flex';
    
    const formData = new FormData();
    formData.append('action', 'get_likes');
    formData.append('resource_id', resourceId);
    
    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const likesList = document.getElementById('likesList');
        if (data.success && data.likes.length > 0) {
            likesList.innerHTML = data.likes.map(like => `
                <div class="like-item">
                    <div class="like-avatar">
                        ${like.profile_image ? 
                            `<img src="${like.profile_image}" alt="${like.user_name}">` :
                            '<i class="fas fa-user"></i>'
                        }
                    </div>
                    <div class="like-info">
                        <strong>${like.user_name || 'Unknown'}</strong>
                        <span>${like.user_email || ''}</span>
                    </div>
                    <span class="like-date">${new Date(like.created_at).toLocaleDateString()}</span>
                </div>
            `).join('');
        } else {
            likesList.innerHTML = '<p class="no-likes">No likes yet</p>';
        }
    });
}

function closeLikesModal() {
    const modal = document.getElementById('likesModal');
    if (modal) modal.style.display = 'none';
}

// Report Functions
function toggleReport(resourceId) {
    const modal = document.getElementById('reportModal');
    if (modal) {
        modal.style.display = 'flex';
        document.getElementById('reportResourceId').value = resourceId;
    }
}

function closeReportModal() {
    const modal = document.getElementById('reportModal');
    if (modal) modal.style.display = 'none';
}

function submitReport() {
    const resourceId = document.getElementById('reportResourceId').value;
    const reason = document.getElementById('reportReason').value;
    
    const formData = new FormData();
    formData.append('action', 'add_report');
    formData.append('resource_id', resourceId);
    formData.append('reason', reason);
    
    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
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
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('profilePreview');
            if (preview) preview.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Country Select - Show/Hide District
function toggleDistrict() {
    const countrySelect = document.getElementById('country');
    const districtGroup = document.getElementById('districtGroup');
    
    if (countrySelect && districtGroup) {
        districtGroup.style.display = countrySelect.value === 'Sri Lanka' ? 'block' : 'none';
        if (countrySelect.value !== 'Sri Lanka') {
            document.getElementById('district').value = '';
        }
    }
}

// Password Strength
function checkPasswordStrength(password) {
    const strengthBar = document.querySelector('.password-strength-bar');
    if (!strengthBar) return;
    
    let strength = 0;
    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    const colors = ['#ef4444', '#f97316', '#eab308', '#22c55e', '#10b981'];
    const width = (strength / 5) * 100;
    
    strengthBar.style.width = width + '%';
    strengthBar.style.backgroundColor = colors[Math.min(strength - 1, 4)] || colors[0];
}

// Initialize scroll reveal
reveal();
