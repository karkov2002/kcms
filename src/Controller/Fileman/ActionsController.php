<?php

namespace Karkov\Kcms\Controller\Fileman;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

include 'system.inc.php';
include 'functions.inc.php';

class ActionsController extends AbstractController
{
    /**
     * @Route("/fileman/dirtree")
     */
    public function dirTree(Request $request)
    {
        ob_start();

        $type = (empty($_GET['type']) ? '' : strtolower($_GET['type']));
        if ('image' != $type && 'flash' != $type) {
            $type = '';
        }

        echo "[\n";
        $tmp = getFilesNumber(fixPath(getFilesPath()), $type);
        echo '{"p":"'.mb_ereg_replace('"', '\\"', getFilesPath()).'","f":"'.$tmp['files'].'","d":"'.$tmp['dirs'].'"}';
        GetDirs(getFilesPath(), $type);
        echo "\n]";

        $out = ob_get_clean();

        return new Response($out);
    }

    /**
     * @Route("/fileman/createdir")
     */
    public function createDir(Request $request)
    {
        ob_start();

        $path = RoxyFile::FixPath(trim(empty($_POST['d']) ? '' : $_POST['d']));
        $name = RoxyFile::FixPath(trim(empty($_POST['n']) ? '' : $_POST['n']));
        verifyPath($path);

        if (is_dir(fixPath($path))) {
            if (mkdir(fixPath($path).'/'.$name, octdec(DIRPERMISSIONS))) {
                echo getSuccessRes();
            } else {
                echo getErrorRes(t('E_CreateDirFailed').' '.basename($path));
            }
        } else {
            echo getErrorRes(t('E_CreateDirInvalidPath'));
        }

        $out = ob_get_clean();

        return new Response($out);
    }

    /**
     * @Route("/fileman/fileslist")
     */
    public function filesList()
    {
        ob_start();

        $path = RoxyFile::FixPath(empty($_POST['d']) ? getFilesPath() : $_POST['d']);
        $type = (empty($_POST['type']) ? '' : strtolower($_POST['type']));
        if ('image' != $type && 'flash' != $type) {
            $type = '';
        }
        verifyPath($path);

        $files = listDirectory(fixPath($path));
        natcasesort($files);
        $str = '';
        echo '[';
        foreach ($files as $f) {
            $fullPath = $path.'/'.$f;
            if (!is_file(fixPath($fullPath)) || ('image' == $type && !RoxyFile::IsImage($f)) || ('flash' == $type && !RoxyFile::IsFlash($f))) {
                continue;
            }
            $size = filesize(fixPath($fullPath));
            $time = filemtime(fixPath($fullPath));
            $w = 0;
            $h = 0;
            if (RoxyFile::IsImage($f)) {
                $tmp = @getimagesize(fixPath($fullPath));
                if ($tmp) {
                    $w = $tmp[0];
                    $h = $tmp[1];
                }
            }
            $str .= '{"p":"'.mb_ereg_replace('"', '\\"', $fullPath).'","s":"'.$size.'","t":"'.$time.'","w":"'.$w.'","h":"'.$h.'"},';
        }
        $str = mb_substr($str, 0, -1);
        echo $str;
        echo ']';

        $out = ob_get_clean();

        return new Response($out);
    }

    /**
     * @Route("/fileman/copydir")
     */
    public function copyDir()
    {
        ob_start();

        $path = RoxyFile::FixPath(trim(empty($_POST['d']) ? '' : $_POST['d']));
        $newPath = RoxyFile::FixPath(trim(empty($_POST['n']) ? '' : $_POST['n']));
        verifyPath($path);
        verifyPath($newPath);

        if (is_dir(fixPath($path))) {
            copyDir(fixPath($path.'/'), fixPath($newPath.'/'.basename($path)));
            echo getSuccessRes();
        } else {
            echo getErrorRes(t('E_CopyDirInvalidPath'));
        }

        $out = ob_get_clean();

        return new Response($out);
    }

    /**
     * @Route("/fileman/deletedir")
     */
    public function deletedir()
    {
        ob_start();

        $path = RoxyFile::FixPath(trim(empty($_GET['d']) ? '' : $_GET['d']));
        verifyPath($path);

        if (is_dir(fixPath($path))) {
            if (fixPath($path.'/') == fixPath(getFilesPath().'/')) {
                echo getErrorRes(t('E_CannotDeleteRoot'));
            } elseif (count(glob(fixPath($path).'/*'))) {
                echo getErrorRes(t('E_DeleteNonEmpty'));
            } elseif (rmdir(fixPath($path))) {
                echo getSuccessRes();
            } else {
                echo getErrorRes(t('E_CannotDeleteDir').' '.basename($path));
            }
        } else {
            echo getErrorRes(t('E_DeleteDirInvalidPath').' '.$path);
        }

        $out = ob_get_clean();

        return new Response($out);
    }

    /**
     * @Route("/fileman/deletefile")
     */
    public function deleteFile()
    {
        ob_start();

        $path = RoxyFile::FixPath(trim($_POST['f']));
        verifyPath($path);

        if (is_file(fixPath($path))) {
            if (unlink(fixPath($path))) {
                echo getSuccessRes();
            } else {
                echo getErrorRes(t('E_DeletеFile').' '.basename($path));
            }
        } else {
            echo getErrorRes(t('E_DeleteFileInvalidPath'));
        }

        $out = ob_get_clean();

        return new Response($out);
    }

    /**
     * @Route("/fileman/copyfile")
     */
    public function copyFile()
    {
        ob_start();

        $path = RoxyFile::FixPath(trim(empty($_POST['f']) ? '' : $_POST['f']));
        $newPath = RoxyFile::FixPath(trim(empty($_POST['n']) ? '' : $_POST['n']));
        if (!$newPath) {
            $newPath = getFilesPath();
        }

        verifyPath($path);
        verifyPath($newPath);

        if (is_file(fixPath($path))) {
            $newPath = $newPath.'/'.RoxyFile::MakeUniqueFilename(fixPath($newPath), basename($path));
            if (copy(fixPath($path), fixPath($newPath))) {
                echo getSuccessRes();
            } else {
                echo getErrorRes(t('E_CopyFile'));
            }
        } else {
            echo getErrorRes(t('E_CopyFileInvalisPath'));
        }

        $out = ob_get_clean();

        return new Response($out);
    }

    /**
     * @Route("/fileman/upload")
     */
    public function upload()
    {
        ob_start();

        $errors = $errorsExt = [];

        $isAjax = (isset($_POST['method']) && 'ajax' == $_POST['method']);
        $path = RoxyFile::FixPath(trim(empty($_POST['d']) ? getFilesPath() : $_POST['d']));
        verifyPath($path);
        $res = '';
        if (is_dir(fixPath($path))) {
            if (!empty($_FILES['files']) && is_array($_FILES['files']['tmp_name'])) {
                $errors = $errorsExt = [];
                foreach ($_FILES['files']['tmp_name'] as $k => $v) {
                    $filename = $_FILES['files']['name'][$k];
                    $filename = RoxyFile::MakeUniqueFilename(fixPath($path), $filename);
                    $filePath = fixPath($path).'/'.$filename;
                    $isUploaded = true;
                    if (!RoxyFile::CanUploadFile($filename)) {
                        $errorsExt[] = $filename;
                        $isUploaded = false;
                    } elseif (!move_uploaded_file($v, $filePath)) {
                        $errors[] = $filename;
                        $isUploaded = false;
                    }
                    if (is_file($filePath)) {
                        @chmod($filePath, octdec(FILEPERMISSIONS));
                    }
                    if ($isUploaded && RoxyFile::IsImage($filename) && (intval(MAX_IMAGE_WIDTH) > 0 || intval(MAX_IMAGE_HEIGHT) > 0)) {
                        RoxyImage::Resize($filePath, $filePath, intval(MAX_IMAGE_WIDTH), intval(MAX_IMAGE_HEIGHT));
                    }
                }
                if ($errors && $errorsExt) {
                    $res = getSuccessRes(t('E_UploadNotAll').' '.t('E_FileExtensionForbidden'));
                } elseif ($errorsExt) {
                    $res = getSuccessRes(t('E_FileExtensionForbidden'));
                } elseif ($errors) {
                    $res = getSuccessRes(t('E_UploadNotAll'));
                } else {
                    $res = getSuccessRes();
                }
            } else {
                $res = getErrorRes(t('E_UploadNoFiles'));
            }
        } else {
            $res = getErrorRes(t('E_UploadInvalidPath'));
        }

        if ($isAjax) {
            if ($errors || $errorsExt) {
                $res = getErrorRes(t('E_UploadNotAll'));
            }
            echo $res;
        } else {
            echo '
        <script>
        parent.fileUploaded('.$res.');
        </script>';
        }

        $out = ob_get_clean();

        return new Response($out);
    }

    /**
     * @Route("/fileman/download")
     */
    public function download()
    {
        ob_start();

        $path = RoxyFile::FixPath(trim($_GET['f']));
        verifyPath($path);

        if (is_file(fixPath($path))) {
            $file = urldecode(basename($path));
            header('Content-Disposition: attachment; filename="'.$file.'"');
            header('Content-Type: application/force-download');
            readfile(fixPath($path));
        }

        $out = ob_get_clean();

        return new Response($out);
    }

    /**
     * @Route("/fileman/downloaddir")
     */
    public function downloadDir()
    {
        ob_start();

        $path = RoxyFile::FixPath(trim($_GET['d']));
        verifyPath($path);
        $path = fixPath($path);

        if (!class_exists('\ZipArchive')) {
            echo '<script>alert("Cannot create zip archive - ZipArchive class is missing. Check your PHP version and configuration");</script>';
        } else {
            try {
                $filename = basename($path);
                $zipFile = $filename.'.zip';
                $zipPath = sys_get_temp_dir().'/'.$zipFile;
                RoxyFile::ZipDir($path, $zipPath);

                header('Content-Disposition: attachment; filename="'.$zipFile.'"');
                header('Content-Type: application/force-download');
                readfile($zipPath);

                register_shutdown_function(__NAMESPACE__.'\deleteTmp', $zipPath);
            } catch (\Exception $ex) {
                echo '<script>alert("'.addslashes(t('E_CreateArchive')).'");</script>';
            }
        }

        $out = ob_get_clean();

        return new Response($out);
    }

    /**
     * @Route("/fileman/movefile")
     */
    public function moveFile()
    {
        ob_start();

        $path = RoxyFile::FixPath(trim(empty($_POST['f']) ? '' : $_POST['f']));
        $newPath = RoxyFile::FixPath(trim(empty($_POST['n']) ? '' : $_POST['n']));
        if (!$newPath) {
            $newPath = getFilesPath();
        }
        verifyPath($path);
        verifyPath($newPath);

        if (!RoxyFile::CanUploadFile(basename($newPath))) {
            echo getErrorRes(t('E_FileExtensionForbidden'));
        } elseif (is_file(fixPath($path))) {
            if (file_exists(fixPath($newPath))) {
                echo getErrorRes(t('E_MoveFileAlreadyExists').' '.basename($newPath));
            } elseif (rename(fixPath($path), fixPath($newPath))) {
                echo getSuccessRes();
            } else {
                echo getErrorRes(t('E_MoveFile').' '.basename($path));
            }
        } else {
            echo getErrorRes(t('E_MoveFileInvalisPath'));
        }

        $out = ob_get_clean();

        return new Response($out);
    }

    /**
     * @Route("/fileman/movedir")
     */
    public function moveDir()
    {
        ob_start();

        $path = RoxyFile::FixPath(trim(empty($_GET['d']) ? '' : $_GET['d']));
        $newPath = RoxyFile::FixPath(trim(empty($_GET['n']) ? '' : $_GET['n']));
        verifyPath($path);
        verifyPath($newPath);

        if (is_dir(fixPath($path))) {
            if (0 === mb_strpos($newPath, $path)) {
                echo getErrorRes(t('E_CannotMoveDirToChild'));
            } elseif (file_exists(fixPath($newPath).'/'.basename($path))) {
                echo getErrorRes(t('E_DirAlreadyExists'));
            } elseif (rename(fixPath($path), fixPath($newPath).'/'.basename($path))) {
                echo getSuccessRes();
            } else {
                echo getErrorRes(t('E_MoveDir').' '.basename($path));
            }
        } else {
            echo getErrorRes(t('E_MoveDirInvalisPath'));
        }

        $out = ob_get_clean();

        return new Response($out);
    }

    /**
     * @Route("/fileman/renamedir")
     */
    public function renameDir()
    {
        ob_start();

        $path = RoxyFile::FixPath(trim(empty($_POST['d']) ? '' : $_POST['d']));
        $name = RoxyFile::FixPath(trim(empty($_POST['n']) ? '' : $_POST['n']));
        verifyPath($path);

        if (is_dir(fixPath($path))) {
            if (fixPath($path.'/') == fixPath(getFilesPath().'/')) {
                echo getErrorRes(t('E_CannotRenameRoot'));
            } elseif (rename(fixPath($path), dirname(fixPath($path)).'/'.$name)) {
                echo getSuccessRes();
            } else {
                echo getErrorRes(t('E_RenameDir').' '.basename($path));
            }
        } else {
            echo getErrorRes(t('E_RenameDirInvalidPath'));
        }

        $out = ob_get_clean();

        return new Response($out);
    }

    /**
     * @Route("/fileman/renamefile")
     */
    public function renameFile()
    {
        ob_start();

        $path = RoxyFile::FixPath(trim(empty($_POST['f']) ? '' : $_POST['f']));
        $name = RoxyFile::FixPath(trim(empty($_POST['n']) ? '' : $_POST['n']));
        verifyPath($path);

        if (is_file(fixPath($path))) {
            if (!RoxyFile::CanUploadFile($name)) {
                echo getErrorRes(t('E_FileExtensionForbidden').' ".'.RoxyFile::GetExtension($name).'"');
            } elseif (rename(fixPath($path), dirname(fixPath($path)).'/'.$name)) {
                echo getSuccessRes();
            } else {
                echo getErrorRes(t('E_RenameFile').' '.basename($path));
            }
        } else {
            echo getErrorRes(t('E_RenameFileInvalidPath'));
        }

        $out = ob_get_clean();

        return new Response($out);
    }

    /**
     * @Route("/fileman/thumb")
     */
    public function thumb()
    {
        ob_start();

        $path = RoxyFile::FixPath(urldecode(empty($_GET['f']) ? '' : $_GET['f']));
        verifyPath($path);

        @chmod(fixPath(dirname($path)), octdec(DIRPERMISSIONS));
        @chmod(fixPath($path), octdec(FILEPERMISSIONS));

        $w = intval(empty($_GET['width']) ? '100' : $_GET['width']);
        $h = intval(empty($_GET['height']) ? '0' : $_GET['height']);

        header('Content-type: '.RoxyFile::GetMIMEType(basename($path)));
        if ($w && $h) {
            RoxyImage::CropCenter(fixPath($path), null, $w, $h);
        } else {
            RoxyImage::Resize(fixPath($path), null, $w, $h);
        }

        $out = ob_get_clean();

        return new Response($out);
    }

    /**
     * @Route("/fileman/conf")
     */
    public function conf()
    {
        $conf = json_decode(file_get_contents(__DIR__.'/conf.json'));

        return new JsonResponse($conf);
    }
}
