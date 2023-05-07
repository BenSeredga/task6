<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <title>Task 6</title>
    <link rel="stylesheet" href="db.css">
</head>
<body>
<?php
    echo '<div class="msgbox">'; 
        if (!empty($messages)) {
            foreach ($messages as $message) {
                print($message);
            }
        }
    echo '</div>';
    $stmt = $db->prepare("SELECT count(person_id) from connects where sup_id = 1;");
    $stmt->execute();
    $god = $stmt->fetchColumn();
    $stmt = $db->prepare("SELECT count(person_id) from connects where sup_id = 2;");
    $stmt->execute();
    $steni = $stmt->fetchColumn();
    $stmt = $db->prepare("SELECT count(person_id) from connects where sup_id = 3;");
    $stmt->execute();
    $levit = $stmt->fetchColumn();
    $stmt = $db->prepare("SELECT count(person_id) from connects where sup_id = 5;");
    $stmt->execute();
    $flash = $stmt->fetchColumn();
    echo "бессмертие: "; echo (empty($god) ? '0' : $god) . "</br>";
    echo "прохождение сквозь стены: "; echo (empty($steni) ? '0' : $steni) . "</br>";
    echo "левитация: "; echo (empty($levit) ? '0' : $levit) . "</br>";
    echo "флеш бег: "; echo (empty($flash) ? '0' : $flash) . "</br>";
?>
    <form action="" method="POST">
        <table>
            <caption>Данные формы</caption>
            <tr> 
                <th>id</th>
                <th>Имя</th>
                <th>email</th>
                <th>Год</th>
                <th>Пол</th>
                <th>Преобладающая рука</th>
                <th>Суперсила</th>
                <th>Биография</th>
            </tr>
            <?php
                foreach ($values as $value) {
                    echo    '<tr>
                            <td style="font-weight: 700;">'; print($value['id']); echo '</td>
                            <td>
                                <input class="input" name="name'.$value['id'].'" value="'; print($value['name']); echo '">
                            </td>
                            <td>
                                <input class="input" name="email'.$value['id'].'" value="'; print($value['email']); echo '">
                            </td>
                            <td>
                                <select name="year'.$value['id'].'">';
                                    for ($i = 2023; $i >= 1922; $i--) {
                                        if ($i == $value['year']) {
                                            printf('<option selected value="%d">%d год</option>', $i, $i);
                                        } else {
                                            printf('<option value="%d">%d год</option>', $i, $i);
                                        }
                                    }
                    echo        '</select>
                            </td>
                            <td> 
                                <div class="column-item">
                                    <input type="radio" id="radioMale'.$value['id'].'" name="gender'.$value['id'].'" value="male" '; if ($value['gender'] == 'male') echo 'checked'; echo '>
                                    <label for="radioMale'.$value['id'].'">Мужчина</label>
                                </div>
                                <div class="column-item">
                                    <input type="radio" id="radioFemale'.$value['id'].'" name="gender'.$value['id'].'" value="female" '; if ($value['gender'] == 'female') echo 'checked'; echo '>
                                    <label for="radioFemale'.$value['id'].'">Женщина</label>
                                </div>
                            </td>
                            <td>
                                <div class="column-item">
                                    <input type="radio" id="radio2'.$value['id'].'" name="limbs'.$value['id'].'" value="2" '; if ($value['limbs'] == 2) echo 'checked'; echo '>
                                    <label for="radio2'.$value['id'].'">2</label>
                                </div>
                                <div class="column-item">
                                    <input type="radio" id="radio3'.$value['id'].'" name="limbs'.$value['id'].'" value="3" '; if ($value['limbs'] == 3) echo 'checked'; echo '>
                                    <label for="radio3'.$value['id'].'">3</label>
                                </div>
                                <div class="column-item">
                                    <input type="radio" id="radio4'.$value['id'].'" name="limbs'.$value['id'].'" value="4" '; if ($value['limbs'] == 4) echo 'checked'; echo '>
                                    <label for="radio4'.$value['id'].'">4</label>
                                </div>
                            </td>';
                    $stmt = $db->prepare("SELECT sup_id FROM connects WHERE person_id = ?");
                    $stmt->execute([$value['id']]);
                    $abilities = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    echo    '<td class="abilities">
                                <div class="column-item">
                                    <input type="checkbox" id="god'.$value['id'].'" name="abilities'.$value['id'].'[]" value="1"' . (in_array(1, $abilities) ? ' checked' : '') . '>
                                    <label for="god'.$value['id'].'">бессмертие</label>
                                </div>
                                <div class="column-item">
                                    <input type="checkbox" id="noclip'.$value['id'].'" name="abilities'.$value['id'].'[]" value="2"' . (in_array(2, $abilities) ? ' checked' : '') . '>
                                    <label for="noclip'.$value['id'].'">прохождение сквозь стены</label>
                                </div>
                                <div class="column-item">
                                    <input type="checkbox" id="levitation'.$value['id'].'" name="abilities'.$value['id'].'[]" value="3"' . (in_array(3, $abilities) ? ' checked' : '') . '>
                                    <label for="levitation'.$value['id'].'">левитация</label>
                                </div>
                                <div class="column-item">
                                    <input type="checkbox" id="flash'.$value['id'].'" name="abilities'.$value['id'].'[]" value="4"' . (in_array(4, $abilities) ? ' checked' : '') . '>
                                    <label for="flash'.$value['id'].'">флеш бег</label>
                                </div>
                            </td>
                            <td>
                                <textarea name="biography'.$value['id'].'" id="" cols="30" rows="4" maxlength="128">'; print $value['biography']; echo '</textarea>
                            </td>
                            <td>
                                <div class="column-item">
                                    <input class="btn" name="save'.$value['id'].'" type="submit" value="save'.$value['id'].'"/>
                                </div>
                                <div class="column-item">
                                    <input class="btn" name="clear'.$value['id'].'" type="submit" value="clear'.$value['id'].'"/>
                                </div>
                            </td>
                        </tr>'; 
                }
            ?>
        </table>
    </form>
</body>
</html>