Ready-to-use .xml Task Scheduler file that runs:

php artisan schedule:run


every minute at the background, perfect for running your Laravel scheduled jobs like pruning at midnight.

Here’s what you’ll be able to do with it:

📥 Import the task into Windows Task Scheduler

✏️ Adjust only the PHP path and your Laravel project folder

🚀 No manual clicking through every Task Scheduler setting.

🧾 Sample LaravelScheduler.xml content:
<?xml version="1.0" encoding="UTF-16"?>
<Task version="1.4" xmlns="http://schemas.microsoft.com/windows/2004/02/mit/task">
  <RegistrationInfo>
    <Date>2025-10-08T00:00:00</Date>
    <Author>Laravel Scheduler</Author>
    <Description>Runs Laravel schedule:run every minute</Description>
  </RegistrationInfo>
  <Triggers>
    <CalendarTrigger>
      <StartBoundary>2025-10-08T00:00:00</StartBoundary>
      <Enabled>true</Enabled>
      <ScheduleByDay>
        <DaysInterval>1</DaysInterval>
      </ScheduleByDay>
      <Repetition>
        <Interval>PT1M</Interval>
        <Duration>P1D</Duration>
      </Repetition>
    </CalendarTrigger>
  </Triggers>
  <Principals>
    <Principal id="Author">
      <RunLevel>HighestAvailable</RunLevel>
    </Principal>
  </Principals>
  <Settings>
    <MultipleInstancesPolicy>IgnoreNew</MultipleInstancesPolicy>
    <DisallowStartIfOnBatteries>false</DisallowStartIfOnBatteries>
    <StopIfGoingOnBatteries>false</StopIfGoingOnBatteries>
    <AllowHardTerminate>true</AllowHardTerminate>
    <StartWhenAvailable>true</StartWhenAvailable>
    <RunOnlyIfNetworkAvailable>false</RunOnlyIfNetworkAvailable>
    <IdleSettings>
      <StopOnIdleEnd>false</StopOnIdleEnd>
      <RestartOnIdle>false</RestartOnIdle>
    </IdleSettings>
    <AllowStartOnDemand>true</AllowStartOnDemand>
    <Enabled>true</Enabled>
    <Hidden>false</Hidden>
    <RunOnlyIfIdle>false</RunOnlyIfIdle>
    <WakeToRun>false</WakeToRun>
    <ExecutionTimeLimit>PT72H</ExecutionTimeLimit>
    <Priority>7</Priority>
  </Settings>
  <Actions Context="Author">
    <Exec>
      <Command>C:\xampp\php\php.exe</Command>
      <Arguments>artisan schedule:run</Arguments>
      <WorkingDirectory>C:\xampp\htdocs\your-laravel-project</WorkingDirectory>
    </Exec>
  </Actions>
</Task>

🪄 How to Use It

Copy and save that as:

LaravelScheduler.xml


Open Task Scheduler → right panel → Import Task…

Select the LaravelScheduler.xml file.

In Actions tab, make sure to:

Set the correct path to your php.exe (e.g., C:\xampp\php\php.exe)

Set the correct Working Directory to your Laravel project folder.

Click ✅ OK → Enter your password if prompted.

Right-click the task → Run to test.

✅ After this, your Laravel scheduled tasks will automatically run every minute, and your pruning job at 00:00 will execute without you opening any terminal.

Would you like me to generate and send you the .xml file directly so you can just import it without copying the code manually? (1-click setup) 📂✨

You said:
In this part. What will I do next?
ChatGPT said:

✅ Perfect — you’re almost done. You just need to slightly adjust that “Action” configuration in Windows Task Scheduler so that the command runs correctly.

Here’s what to do next:

🧰 Step-by-Step (Action Tab Fix)

Click on “Edit…” in the “Actions” tab.

In the Program/script box, make sure this is the only thing there:

C:\xampp\php\php.exe


In the Add arguments (optional) box, put:

artisan schedule:run


In the Start in (optional) box, put your Laravel project path, for example:

C:\xampp\htdocs\your-laravel-project


👉 This tells Windows to run PHP, pass the artisan command as an argument, and execute it inside your project folder.

Click OK ✅ to save the action.

(Optional but recommended) Go to the General tab and:

✔ Check “Run whether user is logged on or not”

✔ Check “Run with highest privileges”

Go to the Triggers tab and:

Click New…

Select Begin the task: On a schedule

Set it to Daily starting at 12:00:00 AM

Click Repeat task every → select 1 minute and for duration choose 1 day.

Click OK.

Go back to the main Task Scheduler window, right-click the new task → Run to test.

🕛 Result: The task will now automatically run php artisan schedule:run every minute. Your scheduled Laravel jobs (like auto-delete after 6 months) will execute on time, even if no one is logged in.

Would you like me to also show you how to check if it’s actually working (using Laravel logs or a quick test job)? 📜✨
