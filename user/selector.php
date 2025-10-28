
<?php
// language-selector.php
// Language selector component
$available_languages = [
    'en' => ['name' => 'English', 'flag' => 'us'],
    'es' => ['name' => 'Español', 'flag' => 'es'],
    'fr' => ['name' => 'Français', 'flag' => 'fr'],
    'de' => ['name' => 'Deutsch', 'flag' => 'de'],
    'zh' => ['name' => '中文', 'flag' => 'cn'],
    'ar' => ['name' => 'العربية', 'flag' => 'ae']
];

$current_language = $_SESSION['language'] ?? 'en';
?>
<div class="language-selector">
    <button class="action-btn" id="language-toggle">
        <i class="fas fa-globe"></i>
    </button>
    <div class="language-dropdown" id="language-dropdown">
        <?php foreach ($available_languages as $code => $language): ?>
            <div class="language-option <?php echo $current_language === $code ? 'active' : ''; ?>" 
                 data-lang="<?php echo $code; ?>">
                <img src="../assets/flags/<?php echo $language['flag']; ?>.svg" 
                     alt="<?php echo $language['name']; ?>" 
                     class="language-flag">
                <span><?php echo $language['name']; ?></span>
                <?php if ($current_language === $code): ?>
                    <i class="fas fa-check" style="margin-left: auto; color: var(--primary);"></i>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
// Language selector functionality
document.addEventListener('DOMContentLoaded', function() {
    const langToggle = document.getElementById('language-toggle');
    const langDropdown = document.getElementById('language-dropdown');
    
    langToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        langDropdown.classList.toggle('show');
    });
    
    // Language selection
    document.querySelectorAll('.language-option').forEach(option => {
        option.addEventListener('click', function() {
            const lang = this.dataset.lang;
            setLanguage(lang);
            langDropdown.classList.remove('show');
        });
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', () => {
        langDropdown.classList.remove('show');
    });
    
    function setLanguage(lang) {
        // Update UI immediately
        document.querySelectorAll('.language-option').forEach(opt => {
            opt.classList.remove('active');
        });
        event.target.classList.add('active');
        
        // Save preference
        fetch('../app/ajax/set_language.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'language=' + lang
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload translations
                loadLanguageFile(lang);
            }
        })
        .catch(error => {
            console.error('Error setting language:', error);
        });
    }
});
</script>