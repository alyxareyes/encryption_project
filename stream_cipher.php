<?php
// --- SETUP & UTILITIES ---
$example_encrypt = "CAT";
$example_decrypt = "00010101 00010111 00000010";

function strToBinary($string) {
    if ($string === "") return "";
    $characters = str_split($string);
    $binary = [];
    foreach ($characters as $char) {
        $binary[] = str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
    }
    return implode(' ', $binary);
}

function binaryToStr($binary) {
    $binary = str_replace(' ', '', $binary); 
    if (strlen($binary) % 8 !== 0) return null; 
    $chars = str_split($binary, 8);
    $str = '';
    foreach ($chars as $char) {
        $str .= chr(bindec($char));
    }
    return $str;
}

// --- LOGIC PROCESSING ---
$res_bin = $res_text = $error = "";
$is_generated = false;
$mode = $_POST['mode'] ?? 'encrypt'; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $input = $_POST['inputString'] ?? '';
    $key = $_POST['keyString'] ?? '';

    if ($mode == 'decrypt' && preg_match('/^[01 ]+$/', $input)) {
        $input = binaryToStr($input) ?? $input;
    }

    if (strlen($input) !== strlen($key)) {
        $error = "Length Mismatch! Input: " . strlen($input) . ", Key: " . strlen($key);
    } else {
        $res_text = $input ^ $key; 
        $res_bin = strToBinary($res_text);
        $input_bin = strToBinary($input);
        $key_bin = strToBinary($key);
        $is_generated = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stream Cipher Engine</title>
    <style>
        body { font-family: 'Courier New', monospace; background: #FFCD90; padding: 20px; color: #333; }
        
        .card { 
            background: #fff; 
            border: 2px solid #FF8C00; 
            padding: 30px; 
            max-width: 600px; 
            margin: auto; 
            border-radius: 12px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
        }
        
        h2 { text-align: center; color: #FF8C00; margin-bottom: 25px; letter-spacing: 2px; }
        
        /* Centered button group in one line */
        .tab-group { 
            display: flex; 
            justify-content: center; 
            align-items: center;
            margin-bottom: 25px; 
            gap: 15px; /* Space between buttons */
        }
        
        .tab-btn { 
            padding: 12px 25px; 
            background: none; 
            color: #FF8C00; 
            border: 2px solid #FF8C00; 
            font-weight: bold; 
            cursor: pointer; 
            border-radius: 8px;
            transition: all 0.3s ease;
            font-family: 'Courier New', monospace;
            text-transform: uppercase;
            min-width: 140px; /* Ensures buttons have uniform size */
        }

        .tab-btn:hover { background: rgba(255, 140, 0, 0.1); }

        .tab-btn.active { 
            background: #FF8C00; 
            color: #fff; 
            box-shadow: 0 4px 8px rgba(255, 140, 0, 0.3);
        }
        
        label { display: block; margin-top: 15px; font-size: 0.85rem; font-weight: bold; color: #555; }
        
        input[type="text"] { 
            width: 100%; 
            padding: 12px; 
            margin-top: 5px; 
            border: 1px solid #ccc; 
            border-radius: 6px; 
            box-sizing: border-box; 
            font-size: 1rem;
        }

        input[type="text"]:focus { border-color: #FF8C00; outline: none; background: #fffaf5; }
        
        .main-submit { 
            width: 100%; 
            padding: 15px; 
            margin-top: 25px; 
            background: #FF8C00; 
            color: #fff; 
            border: none; 
            border-radius: 8px; 
            font-weight: bold; 
            cursor: pointer; 
            font-size: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .main-submit:hover { background: #e67e00; }
        
        .result { background: #fdfdfd; padding: 20px; margin-top: 25px; border-left: 5px solid #FF8C00; border-radius: 4px; border: 1px solid #eee; }
        
        .error { color: #fff; background: #d93025; padding: 12px; text-align: center; border-radius: 6px; margin-bottom: 15px; font-weight: bold; }
        
        .back-btn { display: block; text-align: center; margin-top: 20px; color: #888; font-size: 0.8rem; text-decoration: none; }
        .back-btn:hover { color: #FF8C00; }
    </style>
</head>
<body>

<div class="card">
    <h2>STREAM CIPHER</h2>

    <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>

    <div class="tab-group">
        <form method="post">
            <input type="hidden" name="mode" value="encrypt">
            <button type="submit" class="tab-btn <?= $mode == 'encrypt' ? 'active' : '' ?>">ENCRYPT</button>
        </form>
        <form method="post">
            <input type="hidden" name="mode" value="decrypt">
            <button type="submit" class="tab-btn <?= $mode == 'decrypt' ? 'active' : '' ?>">DECRYPT</button>
        </form>
    </div>

    <form method="post">
        <input type="hidden" name="action" value="generate">
        <input type="hidden" name="mode" value="<?= $mode ?>">

        <?php if ($mode == 'encrypt'): ?>
            <label>ENTER PLAINTEXT (Words):</label>
            <input type="text" name="inputString" placeholder="Ex: <?= $example_encrypt ?>" required value="<?= htmlspecialchars($_POST['inputString'] ?? '') ?>">
            
            <label>ENTER KEY (Secret):</label>
            <input type="text" name="keyString" placeholder="Ex: VVV" required value="<?= htmlspecialchars($_POST['keyString'] ?? '') ?>">
            
            <button type="submit" class="main-submit">GENERATE CIPHER BITS</button>

        <?php else: ?>
            <label>ENTER CIPHER BITS (1s and 0s):</label>
            <input type="text" name="inputString" placeholder="Ex: <?= $example_decrypt ?>" required value="<?= htmlspecialchars($_POST['inputString'] ?? '') ?>">
            
            <label>ENTER KEY (Secret):</label>
            <input type="text" name="keyString" placeholder="Ex: VVV" required value="<?= htmlspecialchars($_POST['keyString'] ?? '') ?>">
            
            <button type="submit" class="main-submit" style="background:#333">GENERATE PLAINTEXT</button>
        <?php endif; ?>
    </form>

    <?php if ($is_generated): ?>
        <div class="result">
            <small style="color:#999; text-transform:uppercase; font-size:0.7rem;">Result as Bits:</small><br>
            <code style="word-break:break-all; color:#FF8C00; font-size:0.9rem;"><?= $res_bin ?></code><br><br>
            <small style="color:#999; text-transform:uppercase; font-size:0.7rem;">Result as Text:</small><br>
            <strong style="font-size:1.3rem; color:#333;"><?= htmlspecialchars($res_text) ?></strong>
        </div>
        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="back-btn">Click here to Reset</a>
    <?php endif; ?>
</div>

</body>
</html>