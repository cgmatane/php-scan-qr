<?php

require_once('./db.php');


function qr_valide($qr) {
   return (strlen($qr) == 7);
}

if (isset($_GET['qr']) && qr_valide($_GET['qr'])) {
	$query = "SELECT passager.prenom, passager.nom 
				FROM commande
				INNER JOIN acheteur ON acheteur.id_acheteur = commande.id_acheteur
				INNER JOIN passager ON passager.id_acheteur = acheteur.id_acheteur
				WHERE commande.qrcode = $1;";

	$qr = $_GET['qr'];

	$result = pg_prepare($dbconn, "my_query", $query);

	$result = pg_execute($dbconn, "my_query", array($qr));
	
	if (pg_num_rows($result) == 0) {
		echo "<span class='error'>Billet invalide</scan>";
	} else {

		echo "<table>\n";
		echo "<tr>\n";
		echo "<th>Prénom</th>";
		echo "<th>Nom</th>"; 		
		echo "</tr>\n";
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
			echo "\t<tr>\n";
			foreach ($line as $col_value) {
				echo "\t\t<td>$col_value</td>\n";
			}
			echo "\t</tr>\n";
		}
		echo "</table>\n";

		// Free resultset
		pg_free_result($result);
	}

	

} else {
	echo "ERREUR";
}

// Closing connection
pg_close($dbconn);

?>