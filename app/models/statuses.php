<?php 
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/models_base.php');

class Statuses extends ModelsBase {
	function get_name_by_id(int $id): ?string {
		$id = intval($id);
		$query = "SELECT `name` FROM `statuses` WHERE `id` = '{$id}' LIMIT 1;";
		$this->connect_to_db();
		$result = $this->connection->query($query);
		$this->close();
		if ($row = $result->fetch_assoc()) return $row['name'];
		return null;
	}

	function get_id_by_name(string $name): ?int {
		$name = $this->clear_input($name);
		$query = "SELECT `id` FROM `statuses` WHERE `name` = '{$name}' LIMIT 1;";
		$this->connect_to_db();
		$result = $this->connection->query($query);
		$this->close();
		if ($row = $result->fetch_assoc()) return intval($row['id']);
		return null;
	}
}
?>
