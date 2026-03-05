#!/usr/bin/env python3
"""
Deploy local WordPress files to production via FTP.

Usage:
    python3 deploy.py                  # Deploy both plugin and theme
    python3 deploy.py plugin           # Deploy only the plugin
    python3 deploy.py theme            # Deploy only the theme
    python3 deploy.py plugin --dry-run # Show what would be uploaded
"""
import ftplib
import os
import sys

def load_env():
    env_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), '.env')
    if os.path.exists(env_path):
        with open(env_path) as f:
            for line in f:
                line = line.strip()
                if line and not line.startswith('#') and '=' in line:
                    key, val = line.split('=', 1)
                    os.environ.setdefault(key.strip(), val.strip())

load_env()

FTP_HOST = os.environ.get("FTP_HOST", "ftp.signature-garage.com")
FTP_USER = os.environ["FTP_USER"]
FTP_PASS = os.environ["FTP_PASS"]

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

TARGETS = {
    "plugin": {
        "local": os.path.join(BASE_DIR, "wp-content/plugins/signature-garage-upgrades"),
        "remote": "/public_html/wp-content/plugins/signature-garage-upgrades",
    },
    "theme": {
        "local": os.path.join(BASE_DIR, "wp-content/themes/signaturecar"),
        "remote": "/public_html/wp-content/themes/signaturecar",
    },
}

IGNORE = {'.DS_Store', 'Thumbs.db', '.git', '__pycache__', 'error_log'}


def ensure_remote_dir(ftp, path):
    dirs = path.strip('/').split('/')
    current = ''
    for d in dirs:
        current += f'/{d}'
        try:
            ftp.cwd(current)
        except ftplib.error_perm:
            ftp.mkd(current)
            ftp.cwd(current)


def upload_dir(ftp, local_dir, remote_dir, dry_run=False):
    count = 0
    for root, dirs, files in os.walk(local_dir):
        dirs[:] = [d for d in dirs if d not in IGNORE]

        rel_path = os.path.relpath(root, local_dir)
        if rel_path == '.':
            current_remote = remote_dir
        else:
            current_remote = f"{remote_dir}/{rel_path}"

        if not dry_run:
            ensure_remote_dir(ftp, current_remote)

        for f in files:
            if f in IGNORE:
                continue
            local_file = os.path.join(root, f)
            remote_file = f"{current_remote}/{f}"

            if dry_run:
                print(f"  [DRY] {remote_file}")
            else:
                print(f"  UPLOAD {remote_file}")
                with open(local_file, 'rb') as fh:
                    ftp.storbinary(f'STOR {remote_file}', fh)
            count += 1

    return count


def main():
    args = sys.argv[1:]
    dry_run = '--dry-run' in args
    args = [a for a in args if a != '--dry-run']

    targets_to_deploy = args if args else ['plugin', 'theme']

    for t in targets_to_deploy:
        if t not in TARGETS:
            print(f"Unknown target: {t}. Use 'plugin' or 'theme'.")
            sys.exit(1)

    if dry_run:
        print("=== DRY RUN (no files will be uploaded) ===\n")

    ftp = None
    if not dry_run:
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)

    total = 0
    for target_name in targets_to_deploy:
        target = TARGETS[target_name]
        local = target["local"]
        remote = target["remote"]

        if not os.path.isdir(local):
            print(f"Local directory not found: {local}")
            continue

        print(f"\n--- Deploying {target_name}: {local} -> {remote} ---")
        count = upload_dir(ftp, local, remote, dry_run)
        total += count
        print(f"  ({count} files)")

    if ftp:
        ftp.quit()

    action = "would upload" if dry_run else "uploaded"
    print(f"\nDone! {total} files {action}.")


if __name__ == "__main__":
    main()
