<?php

include('basic_auth.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $db->prepare("SELECT id, name, year, email, limbs, gender, biography FROM application");
        $stmt->execute();
        $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    $messages = array();

    $errors = array();
    $errors['name'] = !empty($_COOKIE['name_error']);
    $errors['year'] = !empty($_COOKIE['year_error']);
    $errors['email'] = !empty($_COOKIE['email_error']);
    $errors['limbs'] = !empty($_COOKIE['limbs_error']);
    $errors['gender'] = !empty($_COOKIE['gender_error']);
    $errors['abilities'] = !empty($_COOKIE['abilities_error']);
    $errors['biography'] = !empty($_COOKIE['biography_error']);
  
    if ($errors['name']) {
        setcookie('name_error', '', 100000);
        $messages[] = '<div class="error">Заполните имя.</div>';
    }
    
    if ($errors['year']) {
        setcookie('year_error', '', 100000);
        $messages[] = '<div class="error">укажите год.</div>';
    }
    
    if ($errors['email']) {
        setcookie('email_error', '', 100000);
        $messages[] = '<div class="error">Заполните email конкретно.</div>';
    }
    
    if ($errors['limbs']) {
        setcookie('limbs_error', '', 100000);
        $messages[] = '<div class="error">укажите конечности.</div>';
    }
    
    if ($errors['gender']) {
        setcookie('gender_error', '', 100000);
        $messages[] = '<div class="error">Заполните пол.</div>';
    }
    
    if ($errors['abilities']) {
        setcookie('abilities_error', '', 100000);
        $messages[] = '<div class="error">Заполните сверхспособности.</div>';
    }
    
    if ($errors['biography']) {
        setcookie('biography_error', '', 100000);
        $messages[] = '<div class="error">Биография пустая/слишком длинная.</div>';
    }
    include('dbshow.php');
    exit();
} else {
    foreach ($_POST as $key => $value) {
        if (preg_match('/^clear(\d+)$/', $key, $matches)) {
            $app_id = $matches[1];
            setcookie('clear', $app_id, time() + 24 * 60 * 60);
            $stmt = $db->prepare("DELETE FROM application WHERE id = ?");
            $stmt->execute([$app_id]);
            $stmt = $db->prepare("DELETE FROM connects WHERE person_id = ?");
            $stmt->execute([$app_id]);
            $stmt = $db->prepare("DELETE FROM users WHERE person_id = ?");
            $stmt->execute([$app_id]);
        }
        if (preg_match('/^save(\d+)$/', $key, $matches)) {
            $app_id = $matches[1];
            $dates = array();
            $dates['name'] = $_POST['name' . $app_id];
            $dates['email'] = $_POST['email' . $app_id];
            $dates['year'] = $_POST['year' . $app_id];
            $dates['gender'] = $_POST['gender' . $app_id];
            $dates['limbs'] = $_POST['limbs' . $app_id];
            $abilities = $_POST['abilities' . $app_id];
            $dates['biography'] = $_POST['biography' . $app_id];
        
            $name = $dates['name'];
            $email = $dates['email'];
            $year = $dates['year'];
            $gender = $dates['gender'];
            $limbs = $dates['limbs'];
            $biography = $dates['biography'];
        
            $errors = FALSE;
            if (empty($name)) {
              $errors = TRUE;
              setcookie('name_error', '1', time() + 24 * 60 * 60);
            }
            if (empty($year) || !is_numeric($year) || (int)$year<=1923 || (int)$year>=2024) {
              $errors = TRUE;
              setcookie('year_error', '1', time() + 24 * 60 * 60);
            }
            if (empty($email) || !preg_match('/^((([0-9A-Za-z]{1}[-0-9A-z\.]{1,}[0-9A-Za-z]{1})|([0-9А-Яа-я]{1}[-0-9А-я\.]{1,}[0-9А-Яа-я]{1}))@([-A-Za-z]{1,}\.){1,2}[-A-Za-z]{2,})$/u', $email)){
              $errors = TRUE;
              setcookie('email_error', '1', time() + 24 * 60 * 60);
            }
            if ($gender !== 'male' && $gender !== 'female'){
              $errors = TRUE;
              setcookie('gender_error', '1', time() + 24 * 60 * 60);
            }
            if ($limbs !== '1' && $limbs !== '2' && $limbs !== '3' && $limbs !== '4'){  
              $errors = TRUE;
              setcookie('limbs_error', '1', time() + 24 * 60 * 60);
            }
            if (empty($abilities) || !is_array($abilities)) {
              $errors = TRUE;
              setcookie('abilities_error', '1', time() + 24 * 60 * 60);
            }
            if (empty($biography) || strlen($biography) > 128){
              $errors = TRUE;
              setcookie('biography_error', '1', time() + 24 * 60 * 60);
            }
        
            if ($errors) {
                setcookie('error_id', $app_id, time() + 24 * 60 * 60);
                header('Location: index.php');
                exit();
            } else {
                setcookie('name_error', '', 100000);
                setcookie('year_error', '', 100000);
                setcookie('email_error', '', 100000);
                setcookie('gender_error', '', 100000);
                setcookie('limbs_error', '', 100000);
                setcookie('abilities_error', '', 100000);
                setcookie('biography_error', '', 100000); 
            }
            $stmt = $db->prepare("SELECT name, email, year, gender, limbs, biography FROM application WHERE id = ?");
            $stmt->execute([$app_id]);
            $old_dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $db->prepare("SELECT sup_id FROM connects WHERE person_id = ?");
            $stmt->execute([$app_id]);
            $old_abilities = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (array_diff($dates, $old_dates[0])) {
                $stmt = $db->prepare("UPDATE application SET name = ?, year = ?, email = ?, limbs = ?, gender = ?, biography = ? WHERE id = ?");
                $stmt->execute([$dates['name'], $dates['year'], $dates['email'], $dates['limbs'], $dates['gender'], $dates['biography'], $app_id]);
            }
            if (array_diff($abilities, $old_abilities) || count($abilities) != count($old_abilities)) {
                $stmt = $db->prepare("DELETE FROM connects WHERE person_id = ?");
                $stmt->execute([$app_id]);
                $stmt = $db->prepare("INSERT INTO connects (person_id, sup_id) VALUES (?, ?)");
                foreach ($abilities as $superpower_id) {
                    $stmt->execute([$app_id, $superpower_id]);
                }
            }
        }
    }
    header('Location: index.php');
}