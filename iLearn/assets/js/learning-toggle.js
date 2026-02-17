function showSection(sectionId, btn) {
    // Hide all sections
    const sections = document.querySelectorAll('.learning-section');
    sections.forEach(sec => sec.style.display = 'none');

    // Remove 'active' class from all buttons
    const buttons = document.querySelectorAll('.learning-sidebar button');
    buttons.forEach(b => b.classList.remove('active'));

    // Show selected section
    document.getElementById(sectionId).style.display = 'block';

    // Highlight active button
    btn.classList.add('active');
}

// Show reminder by default on page load
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('reminder').style.display = 'block';
});
