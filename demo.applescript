on alfred_script(q)
    tell application "iTerm"
        create window with default profile
    end tell
    tell application "iTerm"
        tell the current session of current window
            write text "sshpass -p 1IEXqpKP#5tKSFJG ssh junling.zheng@8.210.36.22 -p 2222"
        end tell
    end tell
end alfred_script
