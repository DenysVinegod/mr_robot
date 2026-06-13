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

	function create_status(string $name, string $description = ''): ?int {
		$name = $this->clear_input($name);
		$description = $this->clear_input($description);
		$query = "INSERT INTO `statuses` (`name`, `description`) VALUES ('{$name}', '{$description}');";
		$this->connect_to_db();
		$this->connection->query($query);
		$newId = intval($this->connection->insert_id);
		$this->close();
		return $newId > 0 ? $newId : null;
	}

	function get_id_by_name(string $name, bool $createIfMissing = false): ?int {
		$name = $this->clear_input($name);
		$query = "SELECT `id` FROM `statuses` WHERE `name` = '{$name}' LIMIT 1;";
		$this->connect_to_db();
		$result = $this->connection->query($query);
		$this->close();
		if ($row = $result->fetch_assoc()) return intval($row['id']);
		if ($createIfMissing) {
			return $this->create_status($name);
		}
		return null;
	}

	function ensure_statuses(array $names): void {
		foreach ($names as $name) {
			$this->get_id_by_name($name, true);
		}
	}
}
?>
