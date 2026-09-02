<?php
$content = file_get_contents("login.php");
$search = "<div class=\"theme-option\" data-theme=\"sunset\" onclick=\"applyTheme(\"sunset\")\"><div class=\"theme-preview sunset-preview\"></div><span>Sunset</span></div>
            </div>";
$replace = "<div class=\"theme-option\" data-theme=\"sunset\" onclick=\"applyTheme(\"sunset\")\"><div class=\"theme-preview sunset-preview\"></div><span>Sunset</span></div>
                <div class=\"theme-option\" data-theme=\"ultimate-dark\" onclick=\"applyTheme(\"ultimate-dark\")\"><div class=\"theme-preview ultimate-dark-preview\"></div><span>Ultimate Dark</span></div>
                <div class=\"theme-option\" data-theme=\"clean-white\" onclick=\"applyTheme(\"clean-white\")\"><div class=\"theme-preview clean-white-preview\"></div><span>Clean White</span></div>
            </div>";
$content = str_replace($search, $replace, $content);
file_put_contents("login.php", $content);
echo "Done";
?>
