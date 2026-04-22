-- Database structure for ZenFit fitness_trainer

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    goal TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Workouts table
CREATE TABLE IF NOT EXISTS workouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    goal VARCHAR(50),
    difficulty VARCHAR(20),
    duration VARCHAR(20),
    exercises TEXT,
    video_link VARCHAR(255),
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Progress table
CREATE TABLE IF NOT EXISTS progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    weight DECIMAL(5,1),
    calories INT,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Trainers table
CREATE TABLE IF NOT EXISTS trainers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    specialty VARCHAR(100),
    experience VARCHAR(50),
    rating DECIMAL(3,2) DEFAULT 0,
    image_url VARCHAR(255),
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    trainer_id INT NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    status VARCHAR(20) DEFAULT 'Booked',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (trainer_id) REFERENCES trainers(id) ON DELETE CASCADE
);

-- Diet plans table
CREATE TABLE IF NOT EXISTS diet_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    goal VARCHAR(50),
    calories INT,
    meals TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample trainers data
INSERT INTO trainers (name, specialty, experience, rating, image_url, bio) VALUES
('John Smith', 'Weight Loss', '5 years', 4.8, 'https://images.unsplash.com/photo-1567013127542-490d757e51fc?w=400', 'Certified personal trainer specializing in weight loss and strength training'),
('Sarah Johnson', 'Muscle Building', '7 years', 4.9, 'https://images.unsplash.com/photo-1594381898411-846e7d193883?w=400', 'Expert in muscle building and HIIT workouts'),
('Mike Davis', 'Cardio & HIIT', '4 years', 4.7, 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400', 'Passionate about cardio and high-intensity interval training'),
('Emily Brown', 'Yoga & Flexibility', '6 years', 4.9, 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=400', 'Yoga instructor focused on flexibility and mindfulness');

-- Sample workouts data
INSERT INTO workouts (title, goal, difficulty, duration, exercises, video_link, image_url) VALUES
('Full Body HIIT', 'Weight Loss', 'Intermediate', '30 min', 'Jumping Jacks, Burpees, Mountain Climbers, High Knees, Squat Jumps', 'https://www.youtube.com/watch?v=ml6tc2R diko', 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=400'),
('Strength Training', 'Muscle Building', 'Advanced', '45 min', 'Deadlifts, Bench Press, Squats, Rows, Shoulder Press', 'https://www.youtube.com/watch?v=iodx4hP0co4', 'https://images.unsplash.com/photo-1581009146145-5c7aefa9a4bd?w=400'),
('Morning Yoga', 'Flexibility', 'Beginner', '20 min', 'Sun Salutation, Warrior Pose, Tree Pose, Cobra Stretch', 'https://www.youtube.com/watch?v=v7AYKdg6Wyg', 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=400'),
('Cardio Blast', 'Weight Loss', 'Intermediate', '25 min', 'Box Jumps, Jump Rope, Sprint in Place, Lateral Shuffles', 'https://www.youtube.com/watch?v=gC_L9qAHVJ8', 'https://images.unsplash.com/photo-1534258936925-c58bed479fcb?w=400'),
('Core Crusher', 'Muscle Building', 'Intermediate', '15 min', 'Planks, Russian Twists, Leg Raises, Bicycle Crunches', 'https://www.youtube.com/watch?v=5f1cMKF1X0', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400'),
('Beginner Full Body', 'General Fitness', 'Beginner', '20 min', 'Push-ups (modified), Bodyweight Squats, Lunges, Glute Bridges', 'https://www.youtube.com/watch?v=xhCjPJdWcvo', 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=400');

-- Sample diet plans data
INSERT INTO diet_plans (title, goal, calories, meals) VALUES
('Weight Loss Plan', 'Weight Loss', 1200, 'Breakfast: Oatmeal with berries | Lunch: Grilled chicken salad | Dinner: Baked salmon with vegetables | Snacks: Apple, Almonds'),
('Muscle Building Plan', 'Muscle Building', 2500, 'Breakfast: Eggs and avocado toast | Lunch: Chicken breast with rice | Dinner: Steak with potatoes | Snacks: Protein shake, Peanut butter'),
('Balanced Diet Plan', 'General Fitness', 1800, 'Breakfast: Greek yogurt with granola | Lunch: Turkey sandwich | Dinner: Pasta with vegetables | Snacks: Banana, Dark chocolate'),
('Low Carb Plan', 'Weight Loss', 1500, 'Breakfast: Eggs and bacon | Lunch: Chicken salad | Dinner: Grilled fish with spinach | Snacks: Cheese, Nuts');