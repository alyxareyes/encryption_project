<?php

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

$res_bin = ""; 
$res_text = "";
$error = "";
$is_generated = false;
$mode = $_POST['mode'] ?? 'encrypt';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'generate') {
    $input = $_POST['inputString'] ?? '';
    $key = $_POST['keyString'] ?? '';

    if ($mode == 'decrypt' && preg_match('/^[01 ]+$/', $input)) {
        $converted_input = binaryToStr($input);
        if ($converted_input !== null) {
            $input = $converted_input;
        }
    }

    if (strlen($input) !== strlen($key)) {
        $error = "Error: Text and Key must be the same character length! (Input: " . strlen($input) . ", Key: " . strlen($key) . ")";
    } else {
        $xor_result = $input ^ $key;
        $res_bin = strToBinary($xor_result);
        $res_text = $xor_result;
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
        body { 
            font-family: 'Courier New', monospace; 
            background: #FFCD90; 
            color: #FFCD90; 
        }
        
        .card { 
            background: #FFFFFF; 
            border: 2px solid #FF8C00; 
            padding: 30px; 
            max-width: 750px; 
            margin: auto; 
            border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        h2 { text-align: center; text-transform: uppercase; letter-spacing: 3px; margin-bottom: 25px; color: #FF8C00; }
        
        .mode-selector { display: flex; gap: 10px; margin-bottom: 20px; }
        .mode-selector label { 
            flex: 1; 
            padding: 12px; 
            border: 1px solid #FF8C00; 
            color: #FF8C00;
            text-align: center; 
            cursor: pointer; 
            transition: 0.3s; 
            font-size: 0.9rem; 
            border-radius: 4px;
            font-weight: bold;
        }
        .mode-selector input { display: none; }
        .mode-selector input:checked + label { 
            background: #FF8C00; 
            color: #FFFFFF; 
        }

        .field-group { margin-bottom: 15px; }
        label.input-label { display: block; margin-top: 15px; font-size: 0.8rem; color: #666666; text-transform: uppercase; font-weight: bold; }
        
        input[type="text"] { 
            width: 100%; 
            padding: 15px; 
            margin-top: 5px; 
            background: #F9F9F9; 
            border: 1px solid #CCCCCC; 
            color: #333333; 
            box-sizing: border-box; 
            font-size: 1.1rem; 
            border-radius: 4px; 
            outline: none;
        }
        input[type="text"]:focus { border-color: #FF8C00; background: #FFFFFF; }

        .btn-container { display: flex; gap: 10px; margin-top: 25px; }
        button, .back-btn { 
            flex: 1; 
            padding: 18px; 
            font-weight: bold; 
            cursor: pointer; 
            border: none; 
            text-transform: uppercase; 
            font-size: 0.9rem; 
            transition: 0.2s; 
            border-radius: 4px; 
            text-decoration: none; 
            text-align: center; 
        }
        
        button { background: #FF8C00; color: #FFFFFF; }
        button:hover { background: #e67e00; }
        
        .back-btn { background: #333333; color: #FFFFFF; }
        .back-btn:hover { background: #000000; }

        .bit-view { 
            background: #F0F0F0; 
            padding: 20px; 
            margin-top: 30px; 
            border-left: 5px solid #FF8C00; 
            border-radius: 4px; 
        }
        
        .label-tag { color: #FF8C00; font-size: 0.75rem; text-transform: uppercase; display: block; margin-bottom: 5px; font-weight: bold; }
        .val-bin { word-break: break-all; margin-bottom: 15px; display: block; font-size: 0.95rem; letter-spacing: 1px; color: #333333; }
        
        .val-text { 
            font-size: 1.5rem; 
            color: #FF8C00; 
            padding: 10px 15px; 
            display: inline-block; 
            border: 2px solid #FF8C00; 
            border-radius: 4px; 
            background: #FFFFFF;
        }

        .error { 
            color: #FFFFFF; 
            text-align: center; 
            border: 1px solid #ff4444; 
            padding: 12px; 
            margin-bottom: 20px; 
            background: #D93025; 
            border-radius: 4px;
        }
        hr { border: 0; border-top: 1px dashed #FF8C00; margin: 20px 0; }
    </style>
</head>
<body>

<div class="card">
    <h2>Stream Cipher Engine</h2>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="action" value="generate">
        
        <div class="mode-selector">
            <input type="radio" id="enc" name="mode" value="encrypt" onchange="this.form.submit()" <?php echo $mode != 'decrypt' ? 'checked' : ''; ?>>
            <label for="enc">🔒 ENCRYPT</label>
            
            <input type="radio" id="dec" name="mode" value="decrypt" onchange="this.form.submit()" <?php echo $mode == 'decrypt' ? 'checked' : ''; ?>>
            <label for="dec">🔓 DECRYPT</label>
        </div>

        <div class="field-group">
            <label class="input-label"><?php echo $mode == 'decrypt' ? 'Cipher Bits (Input)' : 'Plaintext (Input)'; ?>:</label>
            <input type="text" name="inputString" 
                   placeholder="Ex: <?php echo ($mode == 'decrypt') ? $example_decrypt : $example_encrypt; ?>" 
                   required value="<?php echo htmlspecialchars($_POST['inputString'] ?? ''); ?>">
        </div>

        <div class="field-group">
            <label class="input-label">Secret Key:</label>
            <input type="text" name="keyString" placeholder="Ex: VVV" required value="<?php echo htmlspecialchars($_POST['keyString'] ?? ''); ?>">
        </div>

        <div class="btn-container">
            <button type="submit"><?php echo $mode == 'decrypt' ? 'Decrypt Bits' : 'Encrypt Text'; ?></button>
            <?php if ($is_generated || $error): ?>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="back-btn">← Back to Home</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if ($is_generated): ?>
        <div class="bit-view">
            <span class="label-tag">Input Bits:</span>
            <span class="val-bin"><?php echo $input_bin; ?></span>
            
            <span class="label-tag">Key Bits:</span>
            <span class="val-bin"><?php echo $key_bin; ?></span>
            
            <hr>
            
            <span class="label-tag">Output Bits:</span>
            <span class="val-bin" style="color: #FF8C00; font-weight: bold;"><?php echo $res_bin; ?></span>
            
            <span class="label-tag">Generated Result (Text):</span>
            <span class="val-text"><?php echo htmlspecialchars($res_text); ?></span>
        </div>
    <?php endif; ?>
</div>

</body>
</html>