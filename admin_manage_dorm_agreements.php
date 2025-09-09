<?php
session_start();
require_once 'config.php';
include 'admin_dormitory_header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Dormitory Admin') {
	http_response_code(403);
	echo 'Access denied';
	exit;
}

require_once __DIR__ . '/includes/DormAgreementService.php';
$service = new DormAgreementService($conn);

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$action = isset($_POST['action']) ? $_POST['action'] : '';
	try {
		if ($action === 'create') {
			$title = trim($_POST['title'] ?? '');
			$content = trim($_POST['content'] ?? '');
			$effective = trim($_POST['effective_date'] ?? '');
			$isActive = isset($_POST['is_active']);
			if ($title === '' || $content === '' || $effective === '') {
				throw new Exception('All fields are required.');
			}
			$service->createAgreement($title, $content, $effective, $isActive);
			$success = 'Agreement created successfully.';
		} elseif ($action === 'update') {
			$id = intval($_POST['id'] ?? 0);
			$title = trim($_POST['title'] ?? '');
			$content = trim($_POST['content'] ?? '');
			$effective = trim($_POST['effective_date'] ?? '');
			$isActive = isset($_POST['is_active']);
			if ($id <= 0 || $title === '' || $content === '' || $effective === '') {
				throw new Exception('All fields are required.');
			}
			$service->updateAgreement($id, $title, $content, $effective, $isActive);
			$success = 'Agreement updated successfully.';
		} elseif ($action === 'activate') {
			$id = intval($_POST['id'] ?? 0);
			if ($id <= 0) throw new Exception('Invalid agreement.');
			$service->activateAgreement($id);
			$success = 'Agreement activated.';
		} elseif ($action === 'deactivate') {
			$id = intval($_POST['id'] ?? 0);
			if ($id <= 0) throw new Exception('Invalid agreement.');
			$service->deactivateAgreement($id);
			$success = 'Agreement deactivated.';
		}
	} catch (Throwable $e) {
		$errors[] = $e->getMessage();
	}
}

$agreements = $service->listAgreements();
$active = $service->getActiveAgreement();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Manage Dorm Agreements</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
	<h2>Manage Dormitory Agreements</h2>
	<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
	<?php foreach ($errors as $err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endforeach; ?>

	<div class="card mb-4">
		<div class="card-header">Create / Update Agreement</div>
		<div class="card-body">
			<form method="post">
				<input type="hidden" name="action" value="create">
				<div class="row g-3">
					<div class="col-md-6">
						<label class="form-label">Title</label>
						<input type="text" name="title" class="form-control" required>
					</div>
					<div class="col-md-3">
						<label class="form-label">Effective Date</label>
						<input type="date" name="effective_date" class="form-control" required>
					</div>
					<div class="col-md-3 d-flex align-items-end">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="is_active" id="is_active">
							<label class="form-check-label" for="is_active">Set as Active</label>
						</div>
					</div>
					<div class="col-12">
						<label class="form-label">Content</label>
						<textarea name="content" rows="8" class="form-control" required></textarea>
					</div>
				</div>
				<div class="mt-3">
					<button class="btn btn-primary" type="submit">Create Agreement</button>
				</div>
			</form>
		</div>
	</div>

	<div class="card">
		<div class="card-header">Existing Agreements</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>ID</th>
							<th>Title</th>
							<th>Effective Date</th>
							<th>Active</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($agreements as $ag): ?>
							<tr>
								<td><?= (int)$ag['id'] ?></td>
								<td><?= htmlspecialchars($ag['title']) ?></td>
								<td><?= htmlspecialchars($ag['effective_date']) ?></td>
								<td><?= ((int)$ag['is_active'] === 1) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
								<td>
									<form method="post" class="d-inline">
										<input type="hidden" name="action" value="activate">
										<input type="hidden" name="id" value="<?= (int)$ag['id'] ?>">
										<button class="btn btn-sm btn-outline-success" type="submit" <?= ((int)$ag['is_active'] === 1) ? 'disabled' : '' ?>>Activate</button>
									</form>
									<form method="post" class="d-inline">
										<input type="hidden" name="action" value="deactivate">
										<input type="hidden" name="id" value="<?= (int)$ag['id'] ?>">
										<button class="btn btn-sm btn-outline-secondary" type="submit" <?= ((int)$ag['is_active'] === 0) ? 'disabled' : '' ?>>Deactivate</button>
									</form>
									<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= (int)$ag['id'] ?>">Edit</button>
									<a class="btn btn-sm btn-outline-info" href="download_dorm_agreement.php?id=<?= (int)$ag['id'] ?>">PDF</a>
								</td>
							</tr>
							<div class="modal fade" id="editModal<?= (int)$ag['id'] ?>" tabindex="-1" aria-hidden="true">
								<div class="modal-dialog modal-lg">
									<div class="modal-content">
										<form method="post">
											<input type="hidden" name="action" value="update">
											<input type="hidden" name="id" value="<?= (int)$ag['id'] ?>">
											<div class="modal-header">
												<h5 class="modal-title">Edit Agreement #<?= (int)$ag['id'] ?></h5>
												<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
											</div>
											<div class="modal-body">
												<div class="row g-3">
													<div class="col-md-6">
														<label class="form-label">Title</label>
														<input type="text" name="title" class="form-control" value="<?= htmlspecialchars($ag['title']) ?>" required>
													</div>
													<div class="col-md-3">
														<label class="form-label">Effective Date</label>
														<input type="date" name="effective_date" class="form-control" value="<?= htmlspecialchars($ag['effective_date']) ?>" required>
													</div>
													<div class="col-md-3 d-flex align-items-end">
														<div class="form-check">
															<input class="form-check-input" type="checkbox" name="is_active" id="is_active_<?= (int)$ag['id'] ?>" <?= ((int)$ag['is_active'] === 1) ? 'checked' : '' ?>>
															<label class="form-check-label" for="is_active_<?= (int)$ag['id'] ?>">Set as Active</label>
														</div>
													</div>
													<div class="col-12">
														<label class="form-label">Content</label>
														<textarea name="content" rows="10" class="form-control" required><?= htmlspecialchars($service->getAgreementById((int)$ag['id'])['content']) ?></textarea>
													</div>
												</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
												<button type="submit" class="btn btn-primary">Save changes</button>
											</div>
										</form>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>