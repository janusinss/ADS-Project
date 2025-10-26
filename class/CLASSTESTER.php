<!-- <?php
// Include database connection
require_once '../api/database.php';

// Include all classes
require_once '../class/Profile.php';
require_once '../class/Skill.php';
require_once '../class/Project.php';
require_once '../class/Hobby.php';
require_once '../class/Contact.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "✅ Database connected successfully!\n\n";
    
    // Test Profile class
    $profile = new Profile($db);
    $profiles = $profile->getRecords();
    echo "Profile records: " . count($profiles) . "\n";
    
    // Test Skill class
    $skill = new Skill($db);
    $skills = $skill->getRecords();
    echo "Skill records: " . count($skills) . "\n";
    
    // Test advanced method
    $skillsByCategory = $skill->getSkillsByCategory();
    echo "Skill categories: " . count($skillsByCategory) . "\n";
    
    // Test Project class
    $project = new Project($db);
    $projects = $project->getRecords();
    echo "Project records: " . count($projects) . "\n";
    
    // Test Hobby class
    $hobby = new Hobby($db);
    $hobbies = $hobby->getRecords();
    echo "Hobby records: " . count($hobbies) . "\n";
    
    // Test Contact class
    $contact = new Contact($db);
    $contacts = $contact->getRecords();
    echo "Contact records: " . count($contacts) . "\n";
    
    echo "\n✅ All classes working correctly!";
    
} else {
    echo "❌ Database connection failed!";
}
?> -->