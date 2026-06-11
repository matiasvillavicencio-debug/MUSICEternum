function openAuthModal() {
    window.location.href = '/MUSICEternum/views/login.php';
}

function toggleBandOptions(radioBtn) {
    const bandGroup = document.getElementById('bandMembersGroup');
    if (bandGroup) {
        if (radioBtn.value === 'banda') {
            bandGroup.style.display = 'block';
        } else {
            bandGroup.style.display = 'none';
        }
    }
}

document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

function changeStep(stepId) {
    document.querySelectorAll('.modal-step').forEach(step => step.classList.remove('active'));
    const nextStep = document.getElementById(stepId);
    if (nextStep) {
        nextStep.classList.add('active');
    }
}