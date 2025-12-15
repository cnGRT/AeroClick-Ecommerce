<?php
if (extension_loaded('pdo_mysql')) {
    echo "✅ pdo_mysql is loaded.";
} else {
    echo "❌ pdo_mysql is NOT loaded.";
}
?>