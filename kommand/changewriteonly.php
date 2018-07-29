<?php
require_once ('../Plans.php');
require_once('auth.php');
?>
<html>
<body>
<form method="POST" action="changewriteonly.php">
<?php
if (isset($_REQUEST['username']) && isset($_POST['write-only'])) {
$user = User::get($_REQUEST['username']);
if ($_POST['write-only'] === "true") {
$user->Perms->status = 'write-only';
} else {
$user->Perms->status = '';
}
$user->save();
echo 'Write-only status updated.';
} elseif (isset($_REQUEST['username']) && (($user = User::get($_REQUEST['username'])) !== false)) {
$write_only = ($user->Perms->status == 'write-only');
 ?>
<table><tr><td>Username: </td><td><?php echo $user->username; ?>
<input type="hidden" name="username" value="<?php echo htmlspecialchars($user->username); ?>" /></td></tr>
<tr><td rowspan="2">Write-only: </td>
<td><label><input type="radio" name="write-only" value="true" <?php echo $write_only?"checked ":""?>/>yes</label></td></tr>
<tr><td><label><input type="radio" name="write-only" value="false" <?php echo $write_only?"":"checked " ?>/>no</label></td></tr>
<tr><td /><td><input type="submit" value="Update account" /></td></tr>
</table>
<?php } else { /*!isset(username) */ 
if (isset($_REQUEST['username'])) echo 'Invalid or nonexistent username.<br />';
?>
Username: <input type="text" name="username" value="<?php if (isset($_REQUEST['username'])) echo htmlspecialchars($_REQUEST['username']); ?>" /> 
<input type="submit" value="Look Up" />
<?php } /* !isset(username) */ ?>
</form>
<br /><a href="index.php">Return to Kommand</a>
</body>
</html>
